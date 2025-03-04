<?php

abstract class MockupModel extends \Models\ModelExtends
{
    protected $fakeData = [];

    abstract protected function _pseudoDB();

    public function __construct($id = null)
    {
        $all            = $this->_pseudoDB();
        $this->fakeData = $all[$id] ?? [];
        // Not call parent constructor
    }

    public function __get($name)
    {
        return $this->fakeData[$name] ?? null;
    }

    public function getList($page = self::PAGE_ALL)
    {
        $all   = $this->_pseudoDB();
        $ret   = [];
        $class = get_called_class();
        foreach ($all as $k => $v) {
            $ret[$k] = new $class($k);
        }

        return $ret;
    }

    public function getData()
    {
        return $this->fakeData;
    }

    public function flush()
    {
    }
}
