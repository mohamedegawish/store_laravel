<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function view(User $user, Company $company): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'company_admin' && $user->company_id === $company->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'super_admin';
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->role === 'super_admin') {
            return true;
        }

        return $user->role === 'company_admin' && $user->company_id === $company->id;
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->role === 'super_admin';
    }
}
