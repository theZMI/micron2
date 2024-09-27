<?php

class AccessRuleModel extends MockupModel
{
    protected function _getAllFromPseudoDB()
    {
        return [
            1 => ['id' => 1, 'name' => 'access_to_all_users', 'description' => 'Доступ к списку всех сотрудников'],
            2 => ['id' => 2, 'name' => 'can_make_new_tasks', 'description' => 'Возможность создавать задачи'],
        ];
    }
}