<?php

namespace App\Ports;

use Illuminate\Database\Eloquent\Collection;


interface ITaskRepository
{
    public function findTasks(array $columns = ['*'], $relations = [], $filters = []): Collection;

    public function counttTasks($filters = []);
}
