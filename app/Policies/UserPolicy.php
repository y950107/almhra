<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
     * تحديد من يمكنه رؤية جميع المعلمين.
     */
    public function viewAny(User $user)
    {
        return $user->can('view_any_user');
    }

    /**
     * تحديد من يمكنه رؤية معلم معين.
     */
    public function view(User $user)
    {
        return $user->can('view_user');
    }

    /**
     * تحديد من يمكنه إنشاء معلمين جدد.
     */
    public function create(User $user)
    {
        return $user->can('create_user');
    }

    /**
     * تحديد من يمكنه تحديث بيانات المعلم.
     */
    public function update(User $user)
    {
        return $user->can('update_user');
    }

    /**
     * تحديد من يمكنه حذف معلم معين.
     */
    public function delete(User $user)
    {
        return $user->can('delete_user');
    }

    /**
     * تحديد من يمكنه حذف عدة معلمين دفعة واحدة.
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_user');
    }

    /**
     * تحديد من يمكنه استعادة معلم بعد حذفه.
     */
    public function restore(User $user)
    {
        return $user->can('restore_user');
    }

    /**
     * تحديد من يمكنه حذف معلم نهائيًا.
     */
    public function forceDelete(User $user)
    {
        return $user->can('force_delete_user');
    }
}
