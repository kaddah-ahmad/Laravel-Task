<?php

namespace App\Http\Controllers\Api;

use App\Dto\Tasks\CreateTaskDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\FetchTasksRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $service;

    public function __construct(TaskService $taskService)
    {
        $this->service = $taskService;
    }

    public function createTask(CreateTaskRequest $request)
    {
        $createTaskDto = new CreateTaskDto(
            $request->input('user_id'),
            $request->input('title'),
            $request->input('description'),
            $request->input('due_date'),
            $request->input('status'),
        );

        $task = $this->service->storeTask($createTaskDto);

        return $this->apiResponse([
            'success' => true,
            'message' => 'task created successfully',
            'result' => new TaskResource($task),
            'statusCode' => 201,
        ]);
    }

    public function getTaskById($id)
    {
        $task = $this->service->fetchTask($id);

        return $this->apiResponse([
            'success' => true,
            'message' => 'fetched task successfully',
            'result' => new TaskResource($task),
            'statusCode' => 200,
        ]);
    }

    public function getTasks(FetchTasksRequest $request)
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            $user_id = $request->has('user_id') ? $request->query('user_id') : null;
        } else {
            $user_id = $user->id;
        }

        $page = $request->has('page') ? $request->query('page') : null;
        $limit = $request->has('limit') ? $request->query('limit') : null;
        $status = $request->has('status') ? $request->query('status') : null;
        $due_date = $request->has('due_date') ? $request->query('due_date') : null;

        $tasks = $this->service->fetchTasks($page, $limit, $status, $due_date, $user_id);

        return $this->apiResponse([
            'success' => true,
            'message' => 'get tasks successfully',
            'result' => (object) [
                'task' => new TaskCollection($tasks->tasks),
                'total' => $tasks->totalTasks,
                'current_page' => isset($page) ? $page : null,
            ],
            'statusCode' => 200,
        ]);
    }

    public function updateTask(UpdateTaskRequest $request, $id)
    {
        $inputs = [];
        $request->has('title') ? $inputs['title'] = $request->input('title') : null;
        $request->has('description') ? $inputs['description'] = $request->input('description') : null;
        $request->has('status') ?  $inputs['status']  = $request->input('status') : null;
        $request->has('due_date') ? $inputs['due_date'] = $request->input('due_date') : null;
        $request->has('user_id') ?  $inputs['user_id'] = $request->input('user_id') : null;

        $this->service->updateTask($id, $inputs);

        return $this->apiResponse([
            'success' => true,
            'message' => 'updating task successfully',
            'statusCode' => 200,
        ]);
    }

    public function deleteTask($id)
    {
        $this->service->deleteTask($id);

        return $this->apiResponse([
            'success' => true,
            'message' => 'deleting task successfully',
            'statusCode' => 200,
        ]);
    }
}
