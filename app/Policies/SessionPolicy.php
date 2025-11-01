<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Session;
use App\Models\User;

/**
 * Session Policy
 *
 * Authorization policy for Session model.
 */
class SessionPolicy
{
    /**
     * Determine if the user can view any sessions.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view sessions
    }

    /**
     * Determine if the user can view the session.
     */
    public function view(User $user, Session $session): bool
    {
        // User can view if they are the tutor, enrolled student, or admin
        return $user->id === $session->tutor_id
            || $session->students()->where('user_id', $user->id)->exists()
            || $user->isAdmin();
    }

    /**
     * Determine if the user can create sessions.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher(); // Only teachers can create sessions
    }

    /**
     * Determine if the user can update the session.
     */
    public function update(User $user, Session $session): bool
    {
        return $user->id === $session->tutor_id || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the session.
     */
    public function delete(User $user, Session $session): bool
    {
        return $user->id === $session->tutor_id || $user->isAdmin();
    }
}

