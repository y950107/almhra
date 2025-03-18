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

    //  public function before(User $user, $ability)
    // {
    //     if ($user->hasRole('super_admin')) {
    //         return true;
    //     }
    // }
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_candidate');
    }

    /**
     
     */
    public function view(User $user): bool
    {
        return $user->can('view_candidate') ;
    }

    /**

     */
    public function create(User $user): bool
    {
        return $user->can('create_candidate');
    }

    /**
    
     */
    public function update(User $user): bool
    {
        return $user->can('update_candidate');
    }

    /**
    
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->can('delete_candidate');
    }

    /**

     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_candidate');
    }

    /**
     
     */
    public function sendToInterview(User $user, Candidate $candidate): bool
    {
        return $user->can('send_to_interview_candidate');
    }

    public function accept(User $user): bool
    {
        return $user->can('accept_candidate');
    }
}
