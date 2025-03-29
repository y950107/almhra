<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    /**
     * تحديد الصلاحية العامة (مثلاً، المسؤول له جميع الصلاحيات)
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }

    /**
     * السماح للمستخدم برؤية تقييم معين
     */
    public function view(User $user, Evaluation $evaluation): bool
    {
        return $user->hasPermissionTo('view_evaluation');
    }

    /**
     * السماح للمستخدم برؤية جميع التقييمات
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_evaluation');
    }

    /**
     * السماح بإنشاء تقييم جديد
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_evaluation');
    }

    /**
     * السماح بتحديث تقييم معين
     */
    public function update(User $user, Evaluation $evaluation): bool
    {
        return $user->hasPermissionTo('update_evaluation');
    }

    /**
     * السماح بحذف تقييم معين
     */
    public function delete(User $user, Evaluation $evaluation): bool
    {
        return $user->hasPermissionTo('delete_evaluation');
    }

    /**
     * السماح بحذف أي تقييم
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_any_evaluation');
    }

   
}
