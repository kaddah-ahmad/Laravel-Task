<?php

namespace App\Services;

use App\Dto\Tasks\CreateTaskDto;
use App\Exceptions\InternalServerErrorException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    protected $repository;

    public function __construct(TaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function storeTask(CreateTaskDto $createTaskDto)
    {
        $user = Auth::user();
        if ($user->role === 'user' && $user->id !== $createTaskDto->user_id) {
            throw new UnauthorizedException();
        }

        return $this->repository->create([
            'user_id' => $createTaskDto->user_id,
            'title' => $createTaskDto->title,
            'description' => $createTaskDto->description,
            'due_date' => $createTaskDto->due_date,
            'status' => $createTaskDto->status,
        ]);
    }

    public function fetchTask($id)
    {
        $user = Auth::user();

        $task = $this->repository->findById($id, ['*'], ['user']);

        if (!$task) {
            throw new NotFoundException();
        }

        if ($user->role === 'user' && $user->id !== $task->user_id) {
            throw new UnauthorizedException();
        }

        return $task;
    }

    public function fetchTasks($page, $limit, $status, $due_date, $user_id)
    {
        $filters = [
            'page' => $page,
            'limit' => $limit,
            'status' => $status,
            'due_date' => $due_date,
            'user_id' => $user_id,
        ];

        $tasks = $this->repository->findTasks(['*'], ['user'], $filters);

        $countTasks = $this->repository->counttTasks([
            'status' => $status,
            'due_date' => $due_date,
            'user_id' => $user_id,
        ]);

        return (object) [
            'tasks' => $tasks,
            'totalTasks' => $countTasks
        ];
    }

    public function updateTask($id, $inputs)
    {
        $user = Auth::user();

        $task = $this->repository->findById($id);

        if (!$task) {
            throw new NotFoundException();
        }

        if ($task->user_id !== $user->id && $user->role === 'user') {
            throw new UnauthorizedException();
        }

        $result = $this->repository->update($id, $inputs);

        if (!$result) {
            throw new InternalServerErrorException();
        }
        return $result;
    }

    public function deleteTask($id)
    {
        $user = Auth::user();
        
        $task = $this->repository->findById($id);

        if (!$task) {
            throw new NotFoundException();
        }

        if ($task->user_id !== $user->id && $user->role === 'user') {
            throw new UnauthorizedException();
        }

        $result = $this->repository->delete($id);

        if (!$result) {
            throw new InternalServerErrorException();
        }

        return $result;
    }
}
