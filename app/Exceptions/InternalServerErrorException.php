<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class InternalServerErrorException extends ApplicationException
{
    public function status(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function error(): string
    {
        return 'internal server error';
    }
}