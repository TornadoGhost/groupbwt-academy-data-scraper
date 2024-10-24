<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Retailer;
use App\Repositories\Contracts\RetailerRepositoryInterface;
use Illuminate\Support\Facades\DB;

class RetailerRepository extends BaseRepository implements RetailerRepositoryInterface
{

    public function all()
    {
        if (auth()->user()->isAdmin) {
            return $this->model
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();
        }
        return $this->model
            ->where('isActive', 1)
            ->whereRelation('users', 'id', auth()->id())
            ->get();
    }

    public function delete($uid)
    {
        $retailer = $this->model()
            ->with('users')
            ->where('isActive', 1)
            ->findOrFail($uid);

        return DB::transaction(function () use ($retailer) {
            $retailer->isActive = 0;

            foreach ($retailer->products as $product) {
                $retailer->products()
                    ->updateExistingPivot($product->id, ['isActive' => 0]);
            }

            $retailer->save();
        });
    }

    public function restore($uid)
    {
        $retailer = $this->model()
            ->with('users')
            ->where('isActive', 0)
            ->findOrFail($uid);


        return DB::transaction(function () use ($retailer) {
            $users = $retailer->users;

            foreach ($users as $user) {
                foreach ($user->products as $product) {
                    $product->retailers()
                        ->updateExistingPivot($retailer->id, ['isActive' => 1]);
                }
            }

            $retailer->isActive = 1;
            return $retailer->save();
        });
    }

    public function grandAccess(int $retailer_id, array $users_id)
    {
        $retailer = $this->model
            ->where('isActive', 1)
            ->findOrFail($retailer_id);

        return DB::transaction(function () use ($retailer, $users_id) {
            $result = $retailer->users()->sync($users_id);

            foreach ($result['attached'] as $user_id) {
                $retailerProducts = $retailer->products->where('user_id', $user_id);

                foreach ($retailerProducts as $product) {
                    $product->retailers()
                        ->updateExistingPivot($retailer->id, ['isActive' => 1]);
                }
            }
        });
    }

    public function revokeAccess(int $retailer_id, array $users_id)
    {
        $retailer = $this->model->findOrFail($retailer_id);

        return DB::transaction(function () use ($retailer, $users_id) {
            $products = Product::query()->where('user_id', $users_id)->get();

            foreach ($products as $product) {
                $product->retailers()
                    ->updateExistingPivot($retailer->id, ['isActive' => 0]);
            }

            $retailer->users()->detach($users_id);
        });

    }

    protected function getModelClass(): string
    {
        return Retailer::class;
    }
}
