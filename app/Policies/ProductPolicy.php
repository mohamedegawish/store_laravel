<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view products
    }

    public function view(?User $user, Product $product): bool
    {
        return true; // Anyone can view a specific product
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin' || $user->role === 'company_admin';
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'company_admin' && $user->company_id === $product->company_id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'company_admin' && $user->company_id === $product->company_id;
    }
}
