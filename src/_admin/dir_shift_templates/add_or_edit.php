<?php

$model             = new DirShiftTemplatesModel(+Get('id'));
$users             = (new UserModel())->getList();
$allWorkersIds     = array_keys($users);
$currentWorkersIds = $model->user_ids;
$modelParam        = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};
$possibleParams    = (new ParamModel())->getList();

if (Post('is_set')) {
    $errs       = [];
    $worker_ids = Post('worker_ids');
    $tasks      = Post('tasks'); // [ common => [{task: string, description: string, deadline: int}...], worker_id => [...] ]
    $start_time = strtotime(Post('start_time') . ' 00:00:00');
    $end_time   = strtotime(Post('end_time') . ' 23:59:59');
    $dir_name   = Post('dir_name');
    $dirModel   = new DirShiftTemplatesModel(+Get('id'));
    $wasWorkers = $dirModel->isExists() ? $dirModel->user_ids : [];
    $params     = Post('params');

    // Создаём папку для смен
    $dirModel->name = $dir_name;
    $dir_id         = $dirModel->flush();

    // Удаляем задачи, которые были исключены из смены у каждого юзера
    array_walk($wasWorkers, function ($was_worker_id) use ($worker_ids, $dirModel, $tasks) {
        $foundShiftInDir = (new ShiftModel())->findOne(['dir_id' => $dirModel->id, 'user_id' => +$was_worker_id]);
        $shiftModel      = $foundShiftInDir ?: new ShiftModel();
        $wasUserTasks    = array_map(fn($v) => $v->id, $shiftModel->tasks);
        $userTasks       = array_map(fn($v) => $v['id'], $tasks[$was_worker_id] ?? []);
        array_walk($wasUserTasks, function ($wasUserTask) use ($userTasks) {
            if (in_array($wasUserTask, $userTasks)) {
                return;
            }
            $model = new TaskModel($wasUserTask);
            $model->delete();
        });
    });

    // Если юзер был удалён из списка тех кто участвует в этой смене, то удаляем его сменю
    array_walk($wasWorkers, function ($was_worker_id) use ($worker_ids, $dirModel, $tasks) {
        if (in_array($was_worker_id, $worker_ids)) {
            return;
        }

        $foundShiftInDir = (new ShiftModel())->findOne(['dir_id' => $dirModel->id, 'user_id' => +$was_worker_id]);
        $shiftModel      = $foundShiftInDir ?: new ShiftModel();
        $shiftModel->delete();
    });

    // Создаём смены для каждого пользователя
    foreach ($worker_ids as $worker_id) {
        $foundShiftInDir        = (new ShiftModel())->findOne(['dir_id' => $dirModel->id, 'user_id' => +$worker_id]);
        $shiftModel             = $foundShiftInDir ?: new ShiftModel();
        $shiftModel->user_id    = $worker_id;
        $shiftModel->start_time = $start_time;
        $shiftModel->end_time   = $end_time;
        $shiftModel->dir_id     = $dir_id;
        $shift_id               = $shiftModel->flush();
        $saveShiftTasks         = function() use ($tasks, $worker_id, $shift_id) {
            $commonTasks = $tasks['common'] ?? [];
            $userTasks   = $tasks[$worker_id] ?? [];
            $userTasks   = array_merge($userTasks, $commonTasks);
            foreach ($userTasks as &$v) { // Если id отрицательный, то это временный id-шник который vue создавала для себя (следовательно нам нужно вставить в БД данные и заменить его на настоящий)
                $v['id'] = max($v['id'], 0);
            }
            foreach ($userTasks as $userTask) {
                $taskModel                = new TaskModel($userTask['id'] ?: null);
                $taskModel->task          = $userTask['task'];
                $taskModel->description   = $userTask['description'];
                $taskModel->deadline_time = $userTask['deadline'];
                $taskModel->user_id       = $worker_id;
                $taskModel->shift_id      = $shift_id;
                $taskModel->flush();
            }
        };
        $removeOldShiftParams   = function() use ($shiftModel) {
            $wasShiftParams = $shiftModel->isExists() ? $shiftModel->params : [];
            array_walk($wasShiftParams, fn($m) => $m->delete());
        };
        $saveShiftParams        = function() use ($params, $worker_id, $shift_id) {
            $workerParams = $params[$worker_id] ?? [];
            $commonParams = $params['common'] ?? [];
            $shiftParams  = array_merge($workerParams, $commonParams);
            foreach ($shiftParams as $param_id) {
                $shiftParamModel = new ShiftParamModel();
                $shiftParamModel->shift_id = $shift_id;
                $shiftParamModel->param_id = $param_id;
                $shiftParamModel->flush();
            }
        };

        $saveShiftTasks();
        $removeOldShiftParams();
        $saveShiftParams();
    }

    (new ApiResponse())->normal('OK');
}