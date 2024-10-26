<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RetailerUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $users = [];
        foreach($this->users as $user) {
            $users[] = [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'region_id' => $user->region_id,
                'created_at' => $user->created_at->format('Y-m-d'),
            ];
        }

        return $users;
    }
}
