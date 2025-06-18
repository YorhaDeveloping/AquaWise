<?php

namespace App\Policies;

use App\Models\FishGuide;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FishGuidePolicy
{
    /**
     * Determine whether the user can disable the fish guide.
     */
    public function disable(User $user, FishGuide $fishGuide): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FishGuide $fishGuide): bool
    {
        return $user->hasRole('expert') || $fishGuide->status === 'published';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('expert');
    }

    /**
     * Determine whether the user can enable the fish guide.
     */
    public function enable(User $user, FishGuide $fishGuide): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FishGuide $fishGuide): bool
    {
        return $user->hasRole('expert') && $user->id === $fishGuide->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FishGuide $fishGuide): bool
    {
        return $user->hasRole('admin') || ($user->hasRole('expert') && $user->id === $fishGuide->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FishGuide $fishGuide): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FishGuide $fishGuide): bool
    {
        return false;
    }
}
