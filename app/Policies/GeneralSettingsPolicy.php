<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralSettingsPolicy
{
    use HandlesAuthorization;

    /**
     * السماح للمستخدمين الذين لديهم دور Super Admin بجميع الصلاحيات.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }

    /**
     * الصلاحيات الخاصة بالإعدادات العامة.
     */
    public function view(User $user)
    {
        return $user->can('view_general_settings');
    }

    public function update(User $user)
    {
        return $user->can('update_general_settings');
    }
}
