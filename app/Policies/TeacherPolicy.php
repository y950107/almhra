<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
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
        return $user->can('view_any_teacher');
    }

    /**
     * تحديد من يمكنه رؤية معلم معين.
     */
    public function view(User $user, Teacher $teacher)
    {
        return $user->can('view_teacher');
    }

    /**
     * تحديد من يمكنه إنشاء معلمين جدد.
     */
    public function create(User $user)
    {
        return $user->can('create_teacher');
    }

    /**
     * تحديد من يمكنه تحديث بيانات المعلم.
     */
    public function update(User $user, Teacher $teacher)
    {
        return $user->can('update_teacher');
    }

    /**
     * تحديد من يمكنه حذف معلم معين.
     */
    public function delete(User $user, Teacher $teacher)
    {
        return $user->can('delete_teacher');
    }

    /**
     * تحديد من يمكنه حذف عدة معلمين دفعة واحدة.
     */
    public function deleteAny(User $user)
    {
        return $user->can('delete_any_teacher');
    }

    /**
     * تحديد من يمكنه استعادة معلم بعد حذفه.
     */
    public function restore(User $user, Teacher $teacher)
    {
        return $user->can('restore_teacher');
    }

    /**
     * تحديد من يمكنه حذف معلم نهائيًا.
     */
    public function forceDelete(User $user, Teacher $teacher)
    {
        return $user->can('force_delete_teacher');
    }
}
