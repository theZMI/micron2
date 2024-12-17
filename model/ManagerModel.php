<?php

class ManagerModel extends CommonUserModel
{
    public static string $prefix = 'manager';

    public function __construct($id = null)
    {
        parent::__construct('managers', $id);
    }
}