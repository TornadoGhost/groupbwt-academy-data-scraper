<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        $userId = auth()->user()->id;

        return $this->model()
            ->where('id', '!=', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function create($attributes): Model
    {
        return $this->model()->create([
            'email' => $attributes['email'],
            'username' => $attributes['username'],
            'password' => Hash::make($attributes['password']),
            'region_id' => $attributes['region_id'],
            'isAdmin' => $attributes['isAdmin'] ?? 0,
        ]);
    }

    public function findByEmail(string $email): Model
    {
        return $this->model()->where('email', $email)->firstOrFail();
    }

    public function delete(int $id): bool
    {
        $user = $this->model()->findOrFail($id);
        $userToken = $user->token();

        if ($userToken) {
            $userToken->revoke();
        }

        return $user->delete($id);
    }

    protected function getModelClass(): string
    {
        return User::class;
    }
}
