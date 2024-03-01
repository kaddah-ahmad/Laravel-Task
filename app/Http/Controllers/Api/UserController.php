<?php

namespace App\Http\Controllers\Api;

use App\Dto\Users\UserLoginDto;
use App\Dto\Users\UserRegisterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function register(UserRegisterRequest $request)
    {
        $userRegisterDto = new UserRegisterDto(
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
        );

        $user = $this->service->createUser($userRegisterDto);

        return $this->apiResponse([
            'success' => true,
            'message' => 'user created successfully',
            'result' => new UserResource($user),
            'statusCode' => 201,
        ]);
    }

    public function registerAdmin(UserRegisterRequest $request)
    {
        $userRegisterDto = new UserRegisterDto(
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
        );

        $user = $this->service->createAdmin($userRegisterDto);

        return $this->apiResponse([
            'success' => true,
            'message' => 'admin created successfully',
            'result' => new UserResource($user),
            'statusCode' => 201,
        ]);
    }

    public function login(UserLoginRequest $request)
    {
        $userLoginDto = new UserLoginDto(
            $request->input('email'),
            $request->input('password'),
        );

        $data = $this->service->loginUser($userLoginDto);

        return $this->apiResponse([
            'success' => true,
            'message' => 'user logged-in successfully',
            'result' => (object) [
                'user' => new UserResource($data->user),
                'access_token' => $data->access_token
            ],
            'statusCode' => 200,
        ]);
    }

    public function logout()
    {
        $this->service->logoutUser();

        return $this->apiResponse([
            'success' => true,
            'message' => 'user logged-out successfully',
            'statusCode' => 200,
        ]);
    }
}
