<?php

namespace App\Repositories;

use App\Models\User;
use App\Ports\IUserRepository;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements IUserRepository
{

    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email, array $columns = ['*'], $relations = []): ?Model
    {
        return $this->model
            ->select($columns)
            ->with($relations)
            ->where('email',$email)
            ->first();
    }
}