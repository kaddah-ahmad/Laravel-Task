<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class ValidatorException extends ApplicationException
{

    public function status(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function error(): string | array
    {
        return 'validation error';
    }
}
