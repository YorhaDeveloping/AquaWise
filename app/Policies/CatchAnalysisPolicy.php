<?php

namespace App\Policies;

use App\Models\CatchAnalysis;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CatchAnalysisPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view the list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CatchAnalysis $catchAnalysis): bool
    {
        return $user->id === $catchAnalysis->user_id || $user->hasRole('expert');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create catch analyses
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CatchAnalysis $catchAnalysis): bool
    {
        return $user->id === $catchAnalysis->user_id && !$catchAnalysis->reviewed;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CatchAnalysis $catchAnalysis): bool
    {
        return $user->hasRole('admin') || ($user->id === $catchAnalysis->user_id && !$catchAnalysis->reviewed);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CatchAnalysis $catchAnalysis): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CatchAnalysis $catchAnalysis): bool
    {
        return false;
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, CatchAnalysis $catchAnalysis): bool
    {
        // Expert can review if:
        // 1. They have the expert role
        // 2. They are not the owner of the catch analysis
        // 3. They haven't reviewed this catch yet
        return $user->hasRole('expert') && 
               $user->id !== $catchAnalysis->user_id &&
               !$catchAnalysis->isReviewedBy($user);
    }

    /**
     * Determine whether the user can remove their review from the model.
     */
    public function unreview(User $user, CatchAnalysis $catchAnalysis): bool
    {
        // User can remove review if:
        // 1. They have reviewed this catch
        // 2. They are an admin
        return $user->hasRole('admin') || 
               $catchAnalysis->isReviewedBy($user);
    }
}
