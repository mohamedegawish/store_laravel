<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Customer sees own, admin sees all (scoped in controller)
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'company_admin') {
            return $user->company_id === $order->company_id;
        }

        return $user->id === $order->user_id;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'company_admin' && $user->company_id === $order->company_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->role === 'super_admin';
    }
}
