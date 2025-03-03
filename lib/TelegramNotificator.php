<?php

class TelegramNotificator implements INotificator
{
    public function _request($uri, $curlParams = [])
    {
        $url = 'https://api.telegram.org/bot' . Config('telegram_notificator')['token'] . '/' . $uri;
        $ch  = curl_init();
        curl_setopt_array($ch, array_merge([
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true
        ], $curlParams));

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            "Content-type: application/x-www-form-urlencoded"
//        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function send($userTo, $message, $params = [])
    {
        return $this->_request(
            "sendMessage?chat_id={$userTo->telegram_chat_id}" .
            '&text=' . urlencode($message)
        );
    }

    public function requestPhone($userModel, $btnText = 'Отправить мой номер', $message = 'Нажмите «Отправить мой номер» чтобы поделиться номером телефона')
    {
        $keyboard = [
            'keyboard'          => [
                [
                    [
                        'text'            => urlencode($btnText),
                        'request_contact' => true
                    ]
                ]
            ],
            'resize_keyboard'   => true,
            'one_time_keyboard' => true,
        ];

        return $this->_request(
            "sendMessage?chat_id={$userModel->telegram_chat_id}" .
            '&text=' . urlencode($message) .
            '&reply_markup=' . json_encode($keyboard)
        );
    }

    public function removeKeyboard($userModel, $message = 'Номер получили, спасибо!')
    {
        return $this->_request(
            "sendMessage?chat_id={$userModel->telegram_chat_id}" .
            '&text=' . urlencode($message) .
            '&reply_markup=' . json_encode(['remove_keyboard' => true])
        );
    }

    public function getAvatar($userModel, $pathToSave = null)
    {
        $post     = [
            'user_id' => $userModel->telegram_id,
            'limit'   => 1
        ];
        $response = $this->_request(
            'getUserProfilePhotos',
            [
                [CURLOPT_POST => 1],
                [CURLOPT_POSTFIELDS => http_build_query($post)],
                [CURLOPT_HTTPHEADER => ['Content-type: application/x-www-form-urlencoded']],
            ]
        );

        if (!$response['ok']) {
            return null; // Не удалось получить фотографии профиля пользователя
        }
        if (empty($photos)) {
            return null; // У пользователя нет фотографий профиля
        }

        // Получение информации о файле
        $photo        = $photos[0][0];
        $fileId       = $photo['file_id'];
        $post         = ['file_id' => $fileId];
        $fileResponse = $this->_request(
            'getFile',
            [
                [CURLOPT_POST => 1],
                [CURLOPT_POSTFIELDS => http_build_query($post)],
                [CURLOPT_HTTPHEADER => ['Content-type: application/x-www-form-urlencoded']],
            ]
        );

        if (!$response['ok']) {
            return null; // Не удалось получить информацию о файле
        }

        $filePath       = $fileResponse['result']['file_path'];
        $apiToken       = Config('telegram_notificator')['token'];
        $fileUrl        = "https://api.telegram.org/file/bot$apiToken/$filePath";
        $photoData      = file_get_contents($fileUrl);
        $fileAvatarName = ($userModel->id . '_' . time() . '.jpg');
        $uriFileAvatar  = 'upl/users/' . $fileAvatarName;
        $pathToSave     = $pathToSave ?: (BASEPATH . $uriFileAvatar);

        FileSys::writeFile($pathToSave, $photoData);
        return $uriFileAvatar;
    }
}