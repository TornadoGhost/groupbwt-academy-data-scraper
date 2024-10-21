<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function all($perPage)
    {
        $userId = auth()->user()->id;

        return $this->model()
            ->where('id', '!=', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create($attributes)
    {
        return $this->model()->create([
            'email' => $attributes['email'],
            'username' => $attributes['username'],
            'password' => Hash::make($attributes['password']),
            'region_id' => $attributes['region_id'],
            'isAdmin' => $attributes['isAdmin'] ?? 0,
        ]);
    }

    public function findByEmail($email)
    {
        return $this->model()->where('email', $email)->firstOrFail();
    }

    public function delete($uid)
    {
        $user = $this->model()->findOrFail($uid);
        $userToken = $user->token();

        if ($userToken) {
            $userToken->revoke();
        }

        return $user->delete($uid);
    }

    protected function getModelClass(): string
    {
        return User::class;
    }
}
