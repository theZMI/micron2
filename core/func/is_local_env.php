<?php

// Запущен ли сайт локально (ENV=local в .env). Тогда SMS, геолокация и пр. внешние сервисы заменяются заглушками
function IsLocalEnv(): bool
{
    return Env('ENV') === 'local';
}
