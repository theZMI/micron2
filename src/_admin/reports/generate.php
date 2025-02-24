<?php

$msg = '';

if (Post('is_set')) {
    $time_from = strtotime(Post('time_from') .  ' 00:00:00');
    $time_to   = strtotime(Post('time_to') . ' 23:59:59');
    $shifts    = (new ShiftModel())->find(['time_from' => $time_from, 'time_to' => $time_to]);

    $tasks = [];
    foreach ($shifts as $shift) {
        foreach ($shift->tasks as $task) {
            // Сортируем задачи по одинаковости
            if ( !isset($tasks[$task->task]) ) {
                $tasks[$task->task] = [];
            }
            if ( !isset($tasks[$task->task][$task->user_id]) ) {
                $tasks[$task->task][$task->user_id] = [];
            }
            if ( !isset($tasks[$task->task][$task->user_id][$task->status]) ) {
                $tasks[$task->task][$task->user_id][$task->status] = [];
            }
            // Попить кофе => Михаил Зайцев => Выполнено => [массив из 5 подходящих]
            // Попить кофе => Михаил Зайцев => Не выполнено => [массив из 2 подходящих]
            $tasks[$task->task][$task->user_id][$task->status][] = $task;
        }
    }

    $sorted = ['repeats' => [], 'once' => []];
    foreach ($tasks as $taskName => $users) {
        foreach ($users as $user_id => $statuses) {
            foreach ($statuses as $status => $tasks) {
                $groupKey = count($tasks) > 1 ? 'repeats' : 'once';
                if ( !isset($sorted[$groupKey][$taskName]) ) {
                    $sorted[$groupKey][$taskName] = [];
                }
                if ( !isset($sorted[$groupKey][$taskName][$user_id]) ) {
                    $sorted[$groupKey][$taskName][$user_id] = [];
                }
                if ( !isset($sorted[$groupKey][$taskName][$user_id][$status]) ) {
                    $sorted[$groupKey][$taskName][$user_id][$status] = [];
                }
                $sorted[$groupKey][$taskName][$user_id][$status] = $tasks;
            }
        }
    }

    IncludeCom('_admin/reports/generated', ['tasksByGroups' => $sorted, 'time_from' => $time_from, 'time_to' => $time_to]);
    ExitCom();
}