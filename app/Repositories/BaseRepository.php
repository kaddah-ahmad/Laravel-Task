<?php

namespace App\Repositories;

use App\Ports\IRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class BaseRepository implements IRepository
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function findById(int $id, array $columns = ['*'], $relations = []): ?Model
    {
        return $this->model
            ->select($columns)
            ->with($relations)
            ->find($id);
    }

    public function all(array $columns = ['*'], $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->get($columns);
    }

    public function create(array $attributes): Model
    {
        $model = $this->model->create($attributes);
        return $model->fresh();
    }

    public function update(int $id, array $attributes = [], array $options = []): bool
    {
        return $this->model
            ->find($id)
            ->update($attributes, $options);
    }

    public function save(array $options = []): bool
    {
        return $this->model
            ->save($options);
    }

    public function delete(int $id): bool
    {
        return $this->model
            ->find($id)
            ->delete($id);
    }
}
