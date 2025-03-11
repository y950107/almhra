<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Halaka;
use Illuminate\Auth\Access\HandlesAuthorization;

class HalakaPolicy
{
    use HandlesAuthorization;
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_halaka');
    }

    public function view(User $user, Halaka $halaka): bool
    {
        return $user->can('view_halaka');
    }

    public function create(User $user): bool
    {
        return $user->can('create_halaka');
    }

    public function update(User $user, Halaka $halaka): bool
    {
        return $user->can('update_halaka');
    }

    public function delete(User $user, Halaka $halaka): bool
    {
        return $user->can('delete_halaka');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_halaka');
    }
}
