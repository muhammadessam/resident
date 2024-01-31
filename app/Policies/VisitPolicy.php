<?php

namespace App\Policies;

use App\Enums\Permissions;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return in_array(Permissions::ADD_VISIT->name, $user->permissions);
    }

    public function view(User $user, Visit $visit): bool
    {
        return $user->can('view_visit');
    }

    public function create(User $user): bool
    {
        return in_array(Permissions::ADD_VISIT->name, $user->permissions);
    }

    public function update(User $user, Visit $visit): bool
    {
        return in_array(Permissions::ADD_VISIT->name, $user->permissions);
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $user->can('delete_visit');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_visit');
    }

    public function forceDelete(User $user, Visit $visit): bool
    {
        return $user->can('force_delete_visit');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_visit');
    }

    public function restore(User $user, Visit $visit): bool
    {
        return $user->can('restore_visit');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_visit');
    }

    public function replicate(User $user, Visit $visit): bool
    {
        return $user->can('replicate_visit');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_visit');
    }

}
