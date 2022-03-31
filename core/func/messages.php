<?php

function Msg($message, $css = 'alert-info')
{
    ob_start();
        IncludeCom('_dev/msg', ['message' => $message, 'css' => $css]);
    return ob_get_clean();
}

function MsgOk($message)
{
    return Msg($message, 'alert-success');
}

function MsgErr($message)
{
    return Msg($message, 'alert-danger');
}

function MsgWarning($message)
{
    return Msg($message, 'alert-warning');
}
