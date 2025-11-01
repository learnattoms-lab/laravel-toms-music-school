<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Session;
use App\Models\User;

/**
 * Google Calendar Service Interface
 *
 * Defines the contract for Google Calendar operations.
 */
interface GoogleCalendarServiceInterface
{
    /**
     * Create a Google Calendar event for a session.
     */
    public function createSessionEvent(Session $session): string;

    /**
     * Update a Google Calendar event for a session.
     */
    public function updateSessionEvent(Session $session): string;

    /**
     * Delete a Google Calendar event for a session.
     */
    public function deleteSessionEvent(Session $session): void;

    /**
     * Test Google Calendar connection for a user.
     */
    public function testConnection(User $user): bool;
}

