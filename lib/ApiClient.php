<?php

/**
 * HTTP-клиент для внешнего REST API в формате micron2 (см. ApiResponse): {is_success, is_error, data}
 *
 * Настройки - Config('api_client'), см. core/config/api_client.php
 * Запросы GET/DELETE передают данные в query string, POST/PUT/PATCH - в теле JSON (так их читает FillArrayFromPhpInput)
 *
 *  $response = ApiClient::getInstance()->get('users/5');
 *  $user     = ApiClient::prepareResponse($response); // data или null при ошибке
 */
class ApiClient
{
    use SingletonTrait;

    private array $config;
    private string $authToken;

    private function __construct()
    {
        $this->config    = Config('api_client');
        $this->authToken = (string)$this->config['auth_token'];
    }

    // Токен пользователя (например, полученный после login), заменяет токен из конфига
    public function setAuthToken(string $token): static
    {
        $this->authToken = $token;
        return $this;
    }

    public function url(string $uri = ''): string
    {
        $parts = array_map('rawurlencode', explode('/', ltrim($uri, '/')));
        return rtrim($this->config['root_url'], '/') . '/' . implode('/', $parts);
    }

    // Возвращает data из ответа (или поле $field из data), при ошибке - $default
    public static function prepareResponse(array $response, ?string $field = null, mixed $default = null): mixed
    {
        if ($response['is_error']) {
            return $default;
        }
        $data = $response['data'];
        return $field === null ? $data : ($data[$field] ?? $default);
    }

    public function get(string $uri, array $data = []): array
    {
        return $this->request('GET', $uri, $data);
    }

    public function post(string $uri, array $data = []): array
    {
        return $this->request('POST', $uri, $data);
    }

    public function put(string $uri, array $data = []): array
    {
        return $this->request('PUT', $uri, $data);
    }

    public function patch(string $uri, array $data = []): array
    {
        return $this->request('PATCH', $uri, $data);
    }

    public function delete(string $uri, array $data = []): array
    {
        return $this->request('DELETE', $uri, $data);
    }

    private function log(string $message): void
    {
        if ($this->config['log_file']) {
            FileLogger::create(BASEPATH . 'tmp/' . $this->config['log_file'])->message($message);
        }
    }

    private static function errorResponse(mixed $data): array
    {
        return ['is_success' => false, 'is_error' => true, 'data' => $data];
    }

    private function request(string $method, string $uri, array $data): array
    {
        $url     = $this->url($uri);
        $headers = ['Accept: application/json'];
        if ($this->authToken !== '') {
            $headers[] = 'API-User-Auth-Token: ' . $this->authToken;
        }

        $curlParams = [
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_RETURNTRANSFER => true,
            // Редиректы не проходим: иначе чужой API увёл бы запрос (вместе с токеном API-User-Auth-Token) на другой хост
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => (int)$this->config['timeout'],
            CURLOPT_SSL_VERIFYPEER => (bool)$this->config['ssl_verify'],
            CURLOPT_SSL_VERIFYHOST => $this->config['ssl_verify'] ? 2 : 0,
        ];

        if (in_array($method, ['GET', 'DELETE'], true)) {
            if ($data) {
                $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($data);
            }
        } else {
            $headers[]                      = 'Content-Type: application/json';
            $curlParams[CURLOPT_POSTFIELDS] = json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->log("{$method} {$url}"); // Тела запросов не пишем: там бывают коды, пароли и персональные данные
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, $curlParams + [CURLOPT_HTTPHEADER => $headers]);
        $answer   = curl_exec($ch);
        $curlError = $answer === false ? curl_errno($ch) . ': ' . curl_error($ch) : '';
        curl_close($ch);

        if ($curlError) {
            $this->log("{$method} {$url}\nCURL ERROR {$curlError}");
            return self::errorResponse("Curl error {$curlError}");
        }

        $json = json_decode($answer, true);
        if (!is_array($json)) {
            $this->log("{$method} {$url}\nINCORRECT ANSWER: " . mb_substr($answer, 0, 1000));
            return self::errorResponse(null); // Ответ не в формате JSON-объекта
        }

        $isSuccess = !empty($json['is_success']) && empty($json['is_error']);
        return [
            'is_success' => $isSuccess,
            'is_error'   => !$isSuccess,
            'data'       => $json['data'] ?? null,
        ];
    }
}
