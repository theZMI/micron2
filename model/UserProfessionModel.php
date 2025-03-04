<?php

class UserProfessionModel extends MockupModel
{
    protected function _pseudoDB()
    {
        return [
            1 => ['id' => 1, 'name' => 'Программист'],
            2 => ['id' => 2, 'name' => 'Владелец продукта'],
            3 => ['id' => 3, 'name' => 'Менеджер проекта'],
            4 => ['id' => 4, 'name' => 'Scrum-мастер'],
        ];
    }
}