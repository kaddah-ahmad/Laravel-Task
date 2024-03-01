<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

class UserAlreadyExistsException extends ApplicationException
{
    public function status(): int
    {
        return Response::HTTP_CONFLICT;
    }

    public function error(): string
    {
        return 'user already exists';
    }
}