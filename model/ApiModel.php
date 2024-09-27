<?php

class ApiModel
{
    const LIMIT = 500;

    protected $API;
    protected $_id;
    protected $_table;
    protected $_data = [];
    protected $_dataStart = [];

    public function __construct($table, $id = null)
    {
        $this->API    = Api::getInstance();
        $this->_id    = $id;
        $this->_table = $table;

        if ($this->_id) {
            $data = Api::prepareResponse(
                $this->API->get("{$this->_table}/{$this->_id}")
            );
            $this->initData($data);
        }
    }

    public function initData($data)
    {
        $this->_data = $this->_dataStart = $data;
    }

    // Оборачиваем массив в данный объект чтобы в будущем работать с объектом, а не массивом. Удобно например когда получаем запросом список всех объектов, а потом оборачиваем каждый подмассив в объект
    private static function createFromData($data, $toClass)
    {
        $temp = $toClass == 'AUTO' ? new static() : new $toClass();
        $temp->initData($data);
        return $temp;
    }

    public function arrToObjs($arr, $toClass = 'AUTO')
    {
        $objs = [];
        foreach ($arr as $v) {
            $objs[$v['id']] = self::createFromData($v, $toClass);
        }
        return $objs;
    }

    public function isExists()
    {
        return count($this->_data);
    }

    public function getList()
    {
        return $this->arrToObjs(
            Api::prepareResponse($this->API->get("{$this->_table}"))
        );
    }

    public function __get($key)
    {
        return $this->_data[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    public function flush()
    {
        $hasChanges = $this->_data != $this->_dataStart;
        $isUpdate   = count($this->_dataStart);
        $ret        = false;
        if ($hasChanges) {
            $response = $isUpdate
                ? $this->API->patch("{$this->_table}/{$this->_id}", $this->_data)
                : $this->API->post("{$this->_table}/{$this->_id}", $this->_data);
            $ret      = $response['is_success'];
        }
        return $ret;
    }

    public function getData()
    {
        return $this->_data;
    }
}
