<?php

class Input
{
    private function __construct()
    {
    }

    public static function getInstance()
    {
        static $o = null;
        if (is_null($o)) {
            $o = new self();
        }
        return $o;
    }

    // Очистка от html вставок
    private function htmlClean(&$value)
    {
        $value = htmlspecialchars($value);
    }

    // Очищает входные данные
    public function clean($value, $secureFlags)
    {
        // Если не отключена защита от XSS
        if (!($secureFlags & M_XSS_FILTER_OFF)) {
            global $g_config;
            static $cleaner = null;
            if (is_null($cleaner)) {
                $cleaner = new InputClean($g_config['charset']);
            }
            $value = $cleaner->_clean_input_data($value);
        }

        // Если не отключена защита от HTML
        if (!($secureFlags & M_HTML_FILTER_OFF)) {
            is_array($value)
                ? array_walk_recursive($value, [$this, "HtmlClean"])
                : $this->htmlClean($value);
        }

        return $value;
    }

    /**
     * Параметр из $_GET
     */
    public function get($name, $def = false, $secureFlags = 0)
    {
        return isset($_GET[$name]) ? $this->clean($_GET[$name], $secureFlags) : $def;
    }

    /**
     * Параметр из $_POST
     */
    public function post($name, $def = false, $secureFlags = 0)
    {
        return isset($_POST[$name]) ? $this->clean($_POST[$name], $secureFlags) : $def;
    }

    public function lang($name)
    {
        global $g_lang;

        return $g_lang[$name] ?? $name;
    }
}
