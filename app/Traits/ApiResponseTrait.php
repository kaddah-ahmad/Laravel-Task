<?php

namespace App\Traits;

use Exception;

trait ApiResponseTrait
{
    public function parseGivenData($data = [], $statusCode = 200, $headers = [])
    {
        $responseStructure = [
            'success' => $data['success'],
            'message' => $data['message'] ?? null,
            'result' => $data['result'] ?? null,
        ];

        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['statusCode'])) {
            $statusCode = $data['statusCode'];
        }

        if (isset($data['exception']) && $data['exception'] instanceof Exception) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'error' => $data['exception']->error(),
                    'file' => $data['exception']->getFile(),
                    'line' => $data['exception']->getLine(),
                    'code' => $data['exception']->getCode(),
                    'trace' => $data['exception']->getTrace(),
                ];
            } else {
                $responseStructure['exception'] = [
                    'error' => $data['exception']->error(),
                ];
            }
        }

        if ($data['success'] === false) {
            if (isset($data['error_code'])) {
                $responseStructure['error_code'] = $data['error_code'];
            } else {
                $responseStructure['error_code'] = 1;
            }
        }

        return [
            "content" => $responseStructure,
            "statusCode" => $statusCode,
            "headers" => $headers,
        ];
    }

    protected function apiResponse($data = [], $statusCode = 200, $headers = [])
    {
        $result = $this->parseGivenData($data, $statusCode, $headers);
        return response()->json(
            $result['content'],
            $result['statusCode'],
            $result['headers']
        );
    }
}
