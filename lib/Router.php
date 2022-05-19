<?php

class Router
{
    private $rules;

    private function __construct()
    {
        $this->rules = Config('router_rules');
    }

    public static function getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Из нового адреса получить старый
     */
    public function getOldQuery($query)
    {
        $ret = $query;
        if (count($this->rules)) {
            foreach ($this->rules as $rule) {
                if ($rule->to === $query) {
                    $ret = $rule->from;
                }
            }
        }

        return $ret;
    }

    /**
     * Из старого адреса получить новый
     */
    public function getNewQuery($query)
    {
        $ret = $query;
        if (count($this->rules)) {
            foreach ($this->rules as $rule) {
                if ($rule->from === $query) {
                    $ret = $rule->to;
                }
            }
        }

        return $ret;
    }
}
