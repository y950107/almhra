<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AlMaherRecitation;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlMaherRecitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_al::maher::recitation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('view_al::maher::recitation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_al::maher::recitation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('update_al::maher::recitation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('delete_al::maher::recitation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_al::maher::recitation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AlMaherRecitation $alMaherRecitation): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
