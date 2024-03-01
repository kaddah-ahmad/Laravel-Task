<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UnauthorizedException extends ApplicationException
{
    public function status(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    public function error(): string
    {
        return 'Unauthorized';
    }
}