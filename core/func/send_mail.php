<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function _SendMail($to, $subject, $message, $additionalHeaders, $files, $priority, $config)
{
    $config = array_merge(Config('sendmail'), $config);

    if ($config['send_to_file']) {
        $file = BASEPATH . 'tmp/sent_emails.txt';
        $mail = "---" . PHP_EOL .
            "To: " . $to . PHP_EOL .
            "Subject: " . $subject . PHP_EOL .
            "Message: " . $message . PHP_EOL .
            "Headers: " . $additionalHeaders . PHP_EOL .
            "Files: " . print_r($files, true) . PHP_EOL .
            "Priority: " . $priority;
        FileSys::writeFile($file, $mail, true);
        $ret = 1;
    } else { // Если отсылка на сервере
        $ret  = false;
        $mail = new PHPMailer();
        try {
            $mail->MailerDebug = false;

            // Устанавливает кодировку письма такую же как и кодировка на сайте
            $mail->CharSet = Config('charset');
            $mail->IsHTML(true);

            // Устанавливаем имя отправителя (сначала дефолтное)
            $mail->SetFrom($config['sender'][0], $config['sender'][1]);
            $mail->AddReplyTo($config['replayTo'][0], $config['replayTo'][1]);

            $mail->MailerDebug = false;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ];

            if ($config['smtp']['use']) {
                $mail->IsSMTP();
                $mail->SMTPAuth   = $config['smtp']['auth'];
                $mail->SMTPSecure = $config['smtp']['secure'];
                $mail->Host       = $config['smtp']['host'];
                $mail->Port       = $config['smtp']['port'];

                $mail->Username = $config['smtp']['email'];
                $mail->Password = $config['smtp']['pwd'];

                $mail->SMTPDebug = 0; // 1 = errors and messages, 2 = messages only
            }

            $headers = array_unique(array_filter(explode("\r\n", $additionalHeaders)));
            foreach ($headers as $h) {
                $mail->AddCustomHeader($h);
            }

            $files = array_unique(array_filter($files));
            foreach ($files as $f) {
                $mail->AddAttachment($f);
            }

            $mail->Subject = $subject;
            $mail->AltBody = strip_tags(str_replace("<br>", PHP_EOL, $message));
            $mail->MsgHTML($message);
            $mail->AddAddress($to, $to);
            $ret = $mail->Send();
        } catch (\Throwable $e) {
            ToLog("Can't send email: " . $e->getMessage());
        }
    }
    return $ret;
}

function SendMail($to, $subject, $message, $config = [], $additionalHeaders = '', $files = [])
{
    if (EnvConfig::DEBUG_MODE && strlen($additionalHeaders)) { // TODO
        trigger_error("Sorry, additional headers not work at this moment", E_USER_ERROR);
    }
    if (!is_array($to)) {
        $to = [$to];
    }

    $ret = 0;
    foreach ($to as $email) {
        $SENDMAIL_PRIORITY_NORMAL = 3;
        $ret                      += _SendMail($email, $subject, $message, $additionalHeaders, $files, $SENDMAIL_PRIORITY_NORMAL, $config);
    }
    return $ret;
}