<?php

namespace App\Policies;

use App\Models\ExpertFeedback;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpertFeedbackPolicy
{
    use HandlesAuthorization;

    public function update(User $user, ExpertFeedback $feedback): bool
    {
        // Only allow the expert who created the feedback to update it
        return $user->id === $feedback->expert_id && 
               $user->hasRole('expert') && 
               now()->diffInHours($feedback->created_at) <= 24; // Can only edit within 24 hours
    }

    public function delete(User $user, ExpertFeedback $feedback): bool
    {
        // Only allow the expert who created the feedback or admins to delete it
        return $user->id === $feedback->expert_id || $user->hasRole('admin');
    }

    public function viewAny(User $user): bool
    {
        // Allow any authenticated user to view feedback
        return true;
    }

    public function view(User $user, ExpertFeedback $feedback): bool
    {
        // Allow any authenticated user to view individual feedback
        return true;
    }

    public function create(User $user): bool
    {
        // Only allow verified experts to create feedback
        return $user->hasRole('expert') && 
               $user->expert_verification_status === 'verified';
    }
} 