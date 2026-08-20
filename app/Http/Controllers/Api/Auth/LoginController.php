<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\Auth\LoginUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Services\Auth\LoginUserService;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request, LoginUserService $loginUser)
    {
        $dto = new LoginUserDTO(...$request->validated());

        $result = $loginUser->handle($dto);

        return new AuthResource($result);
    }
}
