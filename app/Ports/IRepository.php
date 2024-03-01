<?php

namespace App\Ports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface IRepository
{
    public function findById(int $id, array $columns = ['*'], $relations = []): ?Model;

    public function all(array $columns = ['*'], $relations = []): Collection;

    public function create(array $attributes): ?Model;

    public function update(int $id, array $attributes = [], array $options = []): bool;

    public function save(array $options = []): bool;

    public function delete(int $id): bool;
}
