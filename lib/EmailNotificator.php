<?php

class EmailNotificator implements INotificator
{
    public function send($userTo, $message, $subject = 'Micron уведомление')
    {
        return SendMail($userTo->email, $subject, $message);
    }
}