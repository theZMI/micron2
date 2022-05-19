<?php

/**
 * Work with $g_config (main app config)
 *
 * Example:
 *  1) Config() - Get all $g_config
 *  2) Config('test') - It's equal $g_config['test']
 *  3) Config('test', 123) - It's equal $g_config['test'] = 123
 *  4) Config(['test1', 'test2']) - It's equal $g_config['test1']['test2']
 *  5) Config(['test1', 'test2'], 123) - It's equal $g_config['test1']['test2'] = 123;
 *  6) Config(['test1', 'test2', 'PUSH'], 123) - It's equal $g_config['test1']['test2'][] = 123;
 *
 * @param $k - key in g_config
 * @param $v - value (if null, then we get value by key
 */
function Config($k = null, $v = null)
{
    global $g_config;
    $k = is_scalar($k) ? [$k] : $k;

    // If called like "Config()" then we try to get all $g_config array
    if (is_null($k)) {
        return $g_config;
    }

    if (is_null($v)) { // We get data
        $config = $g_config;
        foreach ($k as $configKey) {
            if (!isset($config[$configKey])) {
                $config = null;
                break;
            }
            $config = $config[$configKey];
        }
        return $config;
    } else { // We set data
        $config = &$g_config;
        foreach ($k as $configKey) {
            if ($configKey === 'PUSH') {
                $config[] = $v;
                return true;
            }

            if (!isset($config[$configKey])) {
                $config[$configKey] = null;
            }
            $config = &$config[$configKey];
        }
        $config = $v;
        return true;
    }
}