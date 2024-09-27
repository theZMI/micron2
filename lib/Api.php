<?php

class Api
{
    const TOKEN_COOKIE_NAME = "api_auth_token";

    private function __construct()
    {
    }

    public static function getInstance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    public function root(string $uri = ''): string
    {
        $parts = explode("/", $uri);
        array_walk($parts, function (&$v) {
            $v = urlencode($v);
        });

        return AppConfig::BACKEND_ROOT_URL . implode("/", $parts);
    }

    public static function prepareResponse($response, $field = null, $default = null)
    {
        //Xmp($response);
        if ($response['is_error']) {
            return $default;
        }
        $data = $response['data'];
        return $field ? ($data[$field] ?? $default) : $data;
    }

    public static function getCurrentClient($field = null)
    {
        return self::prepareResponse(
            (self::getInstance())->get('clients/current', []),
            $field
        );
    }

    public static function getCurrentManager($field = null)
    {
        return self::prepareResponse(
            (self::getInstance())->get('managers/current', []),
            $field
        );
    }

    private function log($message)
    {
        FileLogger::create(BASEPATH . 'tmp/api_requests.log')->Message($message);
    }

    private function _call($uri, $method, $data = null)
    {
        if ($method !== 'GET') {
            $this->log(sprintf("URL: %s\nMETHOD: %s\nDATA: %s", $uri, $method, print_r($data, true)));
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_USERAGENT      => "Spider", // who am I
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        ];

        $url = $this->root($uri);
//Xmp($url);

        $ch = curl_init($url);

        curl_setopt_array($ch, $options);

        // Auth token
        $token = $_COOKIE[self::TOKEN_COOKIE_NAME] ?? null;
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                "API-Request-Time: " . time(),
                "X-Auth-Token: {$token}"
            ]
        );

        $data = $data ? http_build_query($data, '', '&') : '';

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_POST, 0);

                if ($data) {
                    $url = sprintf("%s?%s", $url, $data);
                }
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $eax  = [
            'is_success' => false,
            'is_error'   => false,
            'data'       => [],
        ];
        $json = [];
        try {
            $answer = curl_exec($ch);

            $json = json_decode($answer, true);
            if (json_last_error()) {
                $json['is_success'] = false;
                $json['is_error']   = true;
                $json['data']       = null; // Incorrect answer format
            }
        } catch (Exception $e) {
            $json['is_success'] = false;
            $json['is_error']   = true;
            $json['data']       = "Error number: " . curl_errno($ch) . PHP_EOL . "Error message: " . curl_error($ch);
        }
        $json = array_merge($eax, $json);
//        $header  = curl_getinfo($ch);
        curl_close($ch);

        if (!$json['is_success'] && !$json['is_error']) {
            $json['is_error'] = true;
        }

        return $json;
    }

    public function get(string $uri, $data = [])
    {
        return $this->_call($uri, 'GET', $data);
    }

    public function post(string $uri, $data = [])
    {
        return $this->_call($uri, 'POST', $data);
    }

    public function put(string $uri, $data = [])
    {
        return $this->_call($uri, 'PUT', $data);
    }

    public function patch(string $uri, $data = [])
    {
        return $this->_call($uri, 'PATCH', $data);
    }

    public function delete(string $uri, $data = [])
    {
        return $this->_call($uri, 'DELETE', $data);
    }
}
