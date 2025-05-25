<?php



namespace App\Policies;

use App\Models\RecitationSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecitationSessionPolicy
{
    use HandlesAuthorization;

    /**
     * السماح برؤية جميع الجلسات
     */

   /* public function before(User $user, $ability)
    {
        dd($user);
        return $user->hasRole('super_admin');
    }*/
    public function viewAny(User $user): bool
    {
        return  $user->hasRole('super_admin') || $user->hasPermissionTo('view_any_recitation::session') ;
    }

    /**
     * السماح برؤية جلسة معينة
     */
    public function view(User $user, RecitationSession $session): bool
    {
        return $user->can('view_recitation::session');
    }

    /**
     * السماح بإنشاء جلسة جديدة
     */
    public function create(User $user): bool
    {
        return $user->can('create_recitation::session');
    }

    /**
     * السماح بتحديث جلسة معينة
     */
    public function update(User $user, RecitationSession $session): bool
    {

        return $user->can('update_recitation::session');
    }

    /**
     * السماح بحذف جلسة معينة
     */
    public function delete(User $user, RecitationSession $session): bool
    {
        return $user->can('delete_recitation::session');
    }

    /**
     * السماح بحذف أي جلسة
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_recitation::session');
    }
}
