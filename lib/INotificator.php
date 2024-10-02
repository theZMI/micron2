<?php

interface INotificator
{
    public function send($userTo, $message);
}