<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected UserServiceInterface $userService)
    {
    }

    public function index(): JsonResponse
    {
        if (auth()->user()->cannot('viewAll', User::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Users list received',
            data: UserResource::collection($this->userService->all())
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', User::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'User created',
            201,
            UserResource::make($this->userService->create($request->validated())));
    }

    public function show(int $id): JsonResponse
    {
        if (auth()->user()->cannot('view', User::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'User received',
            data: UserResource::make($this->userService->find($id)));
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        if (auth()->user()->cannot('update', User::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'User updated',
            data: UserResource::make($this->userService->update($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', User::class)) {
            return $this->unauthorizedResponse();
        }

        $this->userService->delete($id);

        return $this->successResponse('User deleted');
    }
}
