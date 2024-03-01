<?php

namespace App\Ports;

use Illuminate\Database\Eloquent\Model;


interface IUserRepository
{
    public function findByEmail(string $email, array $columns = ['*'], $relations = []): ?Model;
}
