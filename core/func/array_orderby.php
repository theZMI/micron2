<?php

/**
 * Сортировка массива по одному из его параметров
 * Пример:
 *  $dataArray = [
 *     ['name' => 'Second', 'pos' => 2],
 *     ['name' => 'First',  'pos' => 1],
 *     ['name' => 'Third',  'pos' => 3],
 *  ];
 *  $sorted = ArrayOrderBy($dataArray, 'pos', SORT_DESC);
 * @return array
 */
function ArrayOrderBy()
{
    $args = func_get_args();
    $data = array_shift($args);
    if (!is_array($data)) {
        return [];
    }
    $multisort_params = [];
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = [];
            foreach ($data as $row) {
                $tmp[] = $row[$field];
            }
            $args[$n] = $tmp;
        }
        $multisort_params[] = &$args[$n];
    }
    $multisort_params[] = &$data;
    call_user_func_array('array_multisort', $multisort_params);

    return end($multisort_params);
}