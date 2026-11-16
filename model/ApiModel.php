<?php

/**
 * Модель, данные которой хранятся не в своей БД, а во внешнем API (через ApiClient).
 * Интерфейс как у моделей БД: __get/__set, isExists(), getList(), flush(), delete(), getData()
 *
 *  class RemoteUserModel extends ApiModel
 *  {
 *      public function __construct($id = null)
 *      {
 *          parent::__construct('users', $id); // ресурс API: GET users/{id}, PATCH users/{id}, POST users ...
 *      }
 *  }
 *
 *  $user = new RemoteUserModel(5);
 *  $user->name = 'Новое имя';
 *  $user->flush();
 */
class ApiModel
{
    protected ApiClient $api;
    protected string $resource;
    protected array $data      = [];
    protected array $dataStart = []; // Данные в том виде, как их вернул API (для определения изменений)

    public function __construct(string $resource, $id = null)
    {
        $this->api      = ApiClient::getInstance();
        $this->resource = trim($resource, '/');

        if ($id !== null) {
            $data = ApiClient::prepareResponse($this->api->get("{$this->resource}/{$id}"));
            $this->initData(is_array($data) ? $data : []);
        }
    }

    public function initData(array $data): static
    {
        $this->data = $this->dataStart = $data;
        return $this;
    }

    // Оборачивает массивы данных из API в объекты этой же модели (ключ - id)
    public function arrToObjs(array $rows): array
    {
        $ret = [];
        foreach ($rows as $row) {
            $obj = (clone $this)->initData((array)$row);
            if (isset($row['id'])) {
                $ret[$row['id']] = $obj;
            } else {
                $ret[] = $obj;
            }
        }
        return $ret;
    }

    public function isExists(): bool
    {
        return !empty($this->dataStart);
    }

    public function hasChanges(): bool
    {
        return $this->data !== $this->dataStart;
    }

    // $params - фильтры/пагинация в query string, если их поддерживает API
    public function getList(array $params = []): array
    {
        $rows = ApiClient::prepareResponse($this->api->get($this->resource, $params), null, []);
        return is_array($rows) ? $this->arrToObjs($rows) : [];
    }

    // Создаёт (POST resource) или обновляет изменённые поля (PATCH resource/id). true - если API ответил успехом
    public function flush(): bool
    {
        if (!$this->hasChanges()) {
            return true;
        }

        if ($this->isExists()) {
            $response = $this->api->patch("{$this->resource}/{$this->dataStart['id']}", $this->getChanges());
        } else {
            $response = $this->api->post($this->resource, $this->data);
        }

        if ($response['is_success']) {
            // API обычно возвращает сохранённый объект (с id, create_time и т.п.)
            $this->initData(is_array($response['data']) ? $response['data'] + $this->data : $this->data);
        }
        return $response['is_success'];
    }

    public function delete(): bool
    {
        if (!$this->isExists()) {
            return false;
        }
        $response = $this->api->delete("{$this->resource}/{$this->dataStart['id']}");
        if ($response['is_success']) {
            $this->initData([]);
        }
        return $response['is_success'];
    }

    // Только изменённые и новые поля
    public function getChanges(): array
    {
        $ret = [];
        foreach ($this->data as $k => $v) {
            if (!array_key_exists($k, $this->dataStart) || $this->dataStart[$k] !== $v) {
                $ret[$k] = $v;
            }
        }
        return $ret;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function __get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    public function __debugInfo()
    {
        return $this->getData();
    }
}
