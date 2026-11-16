<?php

/**
 * Хранение произвольных значений в одном JSON-поле модели (настройки, доп. параметры и т.п.)
 *
 *  class UserModel extends ModelWithScheme
 *  {
 *      use JsonFieldTrait;
 *
 *      public function jsonFieldName(): string
 *      {
 *          return 'settings'; // TEXT/JSON-колонка в таблице
 *      }
 *  }
 *
 *  $user->setJsonValue('theme', 'dark');
 *  $user->flush();
 *  $user->getJsonValue('theme', 'light');
 */
trait JsonFieldTrait
{
    abstract public function jsonFieldName(): string;

    public function getJsonData(): array
    {
        $fieldName = $this->jsonFieldName();
        $value     = $this->$fieldName;
        $data      = is_string($value) && $value !== '' ? json_decode($value, true) : null;
        return is_array($data) ? $data : [];
    }

    public function setJsonData(array $data): void
    {
        $fieldName        = $this->jsonFieldName();
        $this->$fieldName = json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function getJsonValue(string $name, mixed $default = null): mixed
    {
        return $this->getJsonData()[$name] ?? $default;
    }

    public function setJsonValue(string $name, mixed $value): void
    {
        $data        = $this->getJsonData();
        $data[$name] = $value;
        $this->setJsonData($data);
    }

    public function unsetJsonValue(string $name): void
    {
        $data = $this->getJsonData();
        unset($data[$name]);
        $this->setJsonData($data);
    }
}
