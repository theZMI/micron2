<?php

trait JsonFieldTrait
{
    abstract public function jsonFieldName(): string;


    public function getJsonValue($name, $defaultValue = 0)
    {
        $jsonFieldName = $this->jsonFieldName();
        if (is_null($this->$jsonFieldName)) {
            $ret = $defaultValue;
        } else {
            $s   = json_decode($this->$jsonFieldName);
            $ret = $s->$name ?? $defaultValue;
        }
        return $ret;
    }

    public function setJsonValue($name, $value): void
    {
        $jsonFieldName = $this->jsonFieldName();
        if (is_null($this->$jsonFieldName)) {
            $json = [$name => $value];
        } else {
            $json        = json_decode($this->$jsonFieldName);
            $json->$name = $value;
        }
        $this->$jsonFieldName = json_encode($json);
    }
}
