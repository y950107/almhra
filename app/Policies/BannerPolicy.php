<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Banner;

class BannerPolicy
{
    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض قائمة البنرات.
     */

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

    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض بنر معين.
     */
    public function view(User $user, Banner $banner): bool
    {
        return $user->can('view_banner');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه إنشاء بنر جديد.
     */
    public function create(User $user): bool
    {
        return $user->can('create_banner');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تعديل بيانات البنر.
     */
    public function update(User $user, Banner $banner): bool
    {
        return $user->can('update_banner');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف البنر.
     */
    public function delete(User $user, Banner $banner): bool
    {
        return $user->can('delete_banner');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف عدة بنرات دفعة واحدة.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_banner');
    }
}

