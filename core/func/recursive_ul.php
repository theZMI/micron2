<?php

function _RecursiveUl($type, $list, $attributes = '', $depth = 0, $pPrepareElemFunc = null, $exceptions = array())
{
    // If an array wasn't submitted there's nothing to do...
    if (!is_array($list)) {
        return $list;
    }

    // Set the indentation based on the depth
    $out = str_repeat(" ", $depth);

    // Were any attributes submitted?  If so generate a string
    if (is_array($attributes)) {
        $atts = '';
        foreach ($attributes as $key => $val) {
            $atts .= ' ' . $key . '="' . $val . '"';
        }
        $attributes = $atts;
    }

    // Write the opening list tag
    $out .= "<" . $type . $attributes . ">\n";

    // Cycle through the list elements.  If an array is
    // encountered we will recursively call _RecursiveUl()

    static $_last_list_item = '';
    foreach ($list as $key => $val) {
        $_last_list_item = $key;
        $out             .= str_repeat(" ", $depth + 2);
        $t               = $val;
        if (is_array($t)) {
            if (empty($t)) {
                continue;
            } else {
                $t = is_string(current($t)) ? dirname(current($t)) : $t;
            }
        }


        // Пропуск исключений
        if (is_scalar($t)) {
            $hasInExceptions = false;
            foreach ($exceptions as $e) {
                if (strpos($t, $e) === 0) {
                    $hasInExceptions = true;
                    break;
                }
            }
            if ($hasInExceptions) {
                continue;
            }
        }
        if ($depth === 0) {
            $t               = Config(['admin_list', 'dir']) . $_last_list_item;
            $hasInExceptions = false;
            foreach ($exceptions as $e) {
                if (strpos($t, $e) === 0) {
                    $hasInExceptions = true;
                    break;
                }
            }
            if ($hasInExceptions) {
                continue;
            }
        }


        $out .= "<li>";

        if (!is_array($val)) {
            $out .= $pPrepareElemFunc ? call_user_func($pPrepareElemFunc, $val) : "$val";
        } else {
            $out .= "<strong> <i class='bi bi-folder-fill'></i> &nbsp; " . $_last_list_item . "</strong>\n";
            $out .= _RecursiveUl($type, $val, $attributes, $depth + 4, $pPrepareElemFunc, $exceptions);
            $out .= str_repeat(" ", $depth + 2);
        }

        $out .= "</li>\n";
    }

    // Set the indentation for the closing tag
    $out .= str_repeat(" ", $depth);

    // Write the closing list tag
    $out .= "</" . $type . ">\n";

    return $out;
}

function RecursiveUl($list, $attributes = '', $pPrepareElemFunc = null, $exceptions = [])
{
    return _RecursiveUl('ul', $list, $attributes, 0, $pPrepareElemFunc, $exceptions);
}
