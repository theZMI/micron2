<?php

class ModelWithScheme extends \Models\ModelExtends
{
    public function scheme()
    {
        return [
            'id' => 'int'
        ];
    }

    private function convertToType($type, $value)
    {
        $types = [
            'int'    => fn($value) => (int)$value,
            'float'  => fn($value) => (float)$value,
            'bool'   => fn($value) => (bool)$value,
            'string' => fn($value) => (string)$value,
        ];

        if (array_key_exists($type, $types)) {
            return $types[$type]($value);
        } else {
            throw new InvalidArgumentException("Unsupported type: $type");
        }
    }

    public function getData()
    {
        $ret = parent::getData();
        $scheme = $this->scheme();
        foreach ($ret as $k => $v) {
            if (isset($scheme[$k])) {
                $ret[$k] = $this->convertToType($scheme[$k], $v);
            }
        }
        return $ret;
    }

    public function __get($key)
    {
        $value = parent::__get($key);
        $type  = $this->scheme()[$key] ?? 'string';
        return $this->convertToType($type, $value);
    }

    public function __set($key, $value)
    {
        $type  = $this->scheme()[$key] ?? 'string';
        $value = $this->convertToType($type, $value);
        return parent::__set($key, $value);
    }
}