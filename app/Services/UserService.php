<?php

namespace App\Services;

use App\Dto\Users\UserLoginDto;
use App\Repositories\UserRepository;
use App\Dto\Users\UserRegisterDto;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserAlreadyExistsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function createUser(UserRegisterDto $userRegisterDto)
    {
        $user = $this->repository->findByEmail($userRegisterDto->email);

        if ($user) {
            throw  new UserAlreadyExistsException();
        }

        return $this->repository->create([
            'name' => $userRegisterDto->name,
            'email' => $userRegisterDto->email,
            'role' => 'user',
            'password' => Hash::make($userRegisterDto->password),
        ]);
    }

    public function createAdmin(UserRegisterDto $userRegisterDto)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            throw new UnauthorizedException();
        }

        $user = $this->repository->findByEmail($userRegisterDto->email);

        if ($user) {
            throw  new UserAlreadyExistsException();
        }

        return $this->repository->create([
            'name' => $userRegisterDto->name,
            'email' => $userRegisterDto->email,
            'role' => 'admin',
            'password' => Hash::make($userRegisterDto->password),
        ]);
    }

    public function loginUser(UserLoginDto $userLoginDto)
    {
        $token = Auth::attempt([
            'email' => $userLoginDto->email,
            'password' => $userLoginDto->password
        ]);

        if (!$token) {
            throw new UnauthorizedException();
        }

        $user = Auth::user();

        return (object)[
            'user' => $user,
            'access_token' => $token,
        ];
    }

    public function logoutUser()
    {
        Auth::logout();
    }
}
