<?php



namespace App\Policies;

use App\Models\User;
use App\Models\RecitationSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecitationSessionPolicy
{
    use HandlesAuthorization;

    /**
     * السماح برؤية جميع الجلسات
     */

    public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }
    public function viewAny(User $user): bool
    {
        return $user->can('view_any recitation_session');
    }

    /**
     * السماح برؤية جلسة معينة
     */
    public function view(User $user, RecitationSession $session): bool
    {
        return $user->can('view recitation_session');
    }

    /**
     * السماح بإنشاء جلسة جديدة
     */
    public function create(User $user): bool
    {
        return $user->can('create recitation_session');
    }

    /**
     * السماح بتحديث جلسة معينة
     */
    public function update(User $user, RecitationSession $session): bool
    {
        return $user->can('update recitation_session');
    }

    /**
     * السماح بحذف جلسة معينة
     */
    public function delete(User $user, RecitationSession $session): bool
    {
        return $user->can('delete recitation_session');
    }

    /**
     * السماح بحذف أي جلسة
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any recitation_session');
    }
}
