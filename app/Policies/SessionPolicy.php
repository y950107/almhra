<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Halaka;

class SessionPolicy
{

    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_session');
    }

 
    public function view(User $user, Halaka $session): bool
    {
        return $user->can('view_session');
    }

    public function create(User $user): bool
    {
        return $user->can('create_session');
    }

    
    public function update(User $user, Halaka $session): bool
    {
        return $user->can('update_session');
    }

    
    public function delete(User $user, Halaka $session): bool
    {
        return $user->can('delete_session');
    }

    
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_session');
    }
}
