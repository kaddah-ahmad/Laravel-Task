<?php

namespace App\Repositories;

use App\Models\Task;
use App\Ports\ITaskRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository extends BaseRepository implements ITaskRepository
{

    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }


    public function findTasks(array $columns = ['*'], $relations = [], $filters = []): Collection
    {
        $query = $this->model->query();

        if (!empty($filters)) {
            if (isset($filters['page'])) {
                $query->offset(($filters['page'] - 1) * $filters['limit']);
            }

            if (isset($filters['limit'])) {
                $query->limit($filters['limit']);
            }

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['due_date'])) {
                $query->whereDate('due_date', $filters['due_date']);
            }

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }
        }

        return $query->with($relations)->get($columns);
    }

    public function counttTasks($filters = []): int
    {
        $query = Task::query();

        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        $count = $query->count();

        return $count;
    }
}
