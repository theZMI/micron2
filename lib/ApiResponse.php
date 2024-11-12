<?php

class ApiResponse
{
    private function show($data, $code, $is_success)
    {
        $answer = [
            'is_success' => $is_success,
            'is_error'   => !$is_success,
            'data'       => $data,
        ];

        header(Php::Status($code));
        header('Content-type: application/json');
        die(json_encode($answer));
    }

    public function normal($data, $code = 200): void
    {
        $this->show($data, $code, true);
    }

    public function error($data, $code = 400): void
    {
        $this->show($data, $code, false);
    }
}