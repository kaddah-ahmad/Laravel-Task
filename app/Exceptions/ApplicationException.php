<?php

namespace App\Exceptions;

use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ApplicationException extends Exception
{
    use ApiResponseTrait;

    abstract public function status(): int;

    abstract public function error(): string | array;

    public function render(Request $request)
    {
        return $this->apiResponse([
            'success' => false,
            'message' => 'an error occurred',
            'statusCode' => $this->status(),
            'exception' => $this,
        ]);
    }
}
