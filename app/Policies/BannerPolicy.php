<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Banner;

class BannerPolicy
{
   

      public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    } 
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_banner');
    }

  
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('view_banner');
    }

    public function create(User $user): bool
    {
        return $user->can('create_banner');
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->can('update_banner');
    }

  
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('delete_banner');
    }

  
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_banner');
    }
}

