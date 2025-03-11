<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;

class StudentPolicy
{

    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }
    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض قائمة الطلاب.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_student');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه عرض طالب معين.
     */
    public function view(User $user, Student $student): bool
    {
        return $user->can('view_student');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه إنشاء طالب جديد.
     */
    public function create(User $user): bool
    {
        return $user->can('create_student');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تعديل بيانات الطالب.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->can('update_student');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف طالب.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->can('delete_student');
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف أي طالب.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_student');
    }
}

