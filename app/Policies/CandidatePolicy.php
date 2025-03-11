<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Candidate;
use Illuminate\Auth\Access\HandlesAuthorization;

class CandidatePolicy
{
    use HandlesAuthorization;

    /**
     * السماح بعرض جميع المترشحين.
     */

     public function before(User $user, $ability)
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
    }
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_candidate');
    }

    /**
     * السماح بعرض مترشح معين.
     */
    public function view(User $user, Candidate $candidate): bool
    {
        return $user->can('view_candidate');
    }

    /**
     * السماح بإنشاء مترشح جديد.
     */
    public function create(User $user): bool
    {
        return $user->can('create_candidate');
    }

    /**
     * السماح بتحديث بيانات المترشح.
     */
    public function update(User $user, Candidate $candidate): bool
    {
        return $user->can('update_candidate');
    }

    /**
     * السماح بحذف مترشح معين.
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->can('delete_candidate');
    }

    /**
     * السماح بحذف عدة مترشحين دفعة واحدة.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_candidate');
    }

    /**
     * السماح بإرسال المترشح إلى المقابلة.
     */
    public function sendToInterview(User $user, Candidate $candidate): bool
    {
        return $user->can('send_to_interview_candidate');
    }

    /**
     * السماح بقبول المترشح وتحويله إلى طالب.
     */
    public function accept(User $user, Candidate $candidate): bool
    {
        return $user->can('accept_candidate');
    }
}
