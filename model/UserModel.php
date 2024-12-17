<?php

/**
 * Пользовательская модель
 */
class UserModel extends CommonUserModel
{
    public static string $prefix = 'user'; // Префикс для g_config с настройками модели (конфиги по авторизации, путей для аватарки и картинок и др.)

    public function __construct($id = null)
    {
        parent::__construct('users', $id);
    }
}