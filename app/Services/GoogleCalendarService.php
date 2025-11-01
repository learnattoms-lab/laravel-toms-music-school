<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\OAuthCredential;
use App\Models\Session;
use App\Models\User;
use App\Services\Interfaces\GoogleCalendarServiceInterface;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_ConferenceData;
use Google_Service_Calendar_CreateConferenceRequest;
use Illuminate\Support\Facades\Log;

/**
 * Google Calendar Service
 *
 * Handles Google Calendar integration for session management.
 *
 * Note: Requires google/apiclient package
 * Install via: composer require google/apiclient
 */
class GoogleCalendarService implements GoogleCalendarServiceInterface
{
    private Google_Client $googleClient;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    private function initializeGoogleClient(): void
    {
        $this->googleClient = new Google_Client();
        $this->googleClient->setClientId(config('services.google.client_id'));
        $this->googleClient->setClientSecret(config('services.google.client_secret'));
        $this->googleClient->setRedirectUri(config('services.google.redirect_uri'));
        $this->googleClient->setScopes([
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ]);
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('consent');
    }

    /**
     * Create a Google Calendar event for a session.
     */
    public function createSessionEvent(Session $session): string
    {
        try {
            $tutor = $session->tutor;
            if (!$tutor) {
                throw new \Exception('Session must have a tutor');
            }

            // Get or refresh OAuth credentials for the tutor
            $credentials = $this->getValidCredentials($tutor);
            if (!$credentials) {
                throw new \Exception('Tutor does not have valid Google OAuth credentials. Please connect your Google account.');
            }

            $this->googleClient->setAccessToken($credentials->access_token);

            // Refresh token if needed
            if ($credentials->needsRefresh()) {
                $this->refreshToken($credentials);
            }

            $calendarService = new Google_Service_Calendar($this->googleClient);

            // Create the event
            $event = new Google_Service_Calendar_Event();
            $event->setSummary($session->getSessionTitle());
            $event->setDescription($session->notes ?? 'Music lesson session');

            // Set start time
            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime($session->start_at_utc->toRfc3339String());
            $startDateTime->setTimeZone($tutor->timezone ?? 'UTC');
            $event->setStart($startDateTime);

            // Set end time
            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endDateTime->setDateTime($session->end_at_utc->toRfc3339String());
            $endDateTime->setTimeZone($tutor->timezone ?? 'UTC');
            $event->setEnd($endDateTime);

            // Add attendees (students)
            $attendees = [];
            foreach ($session->getEnrolledStudents() as $student) {
                $attendees[] = ['email' => $student->email];
            }
            if (!empty($attendees)) {
                $event->setAttendees($attendees);
            }

            // Create Google Meet conference
            $conferenceData = new Google_Service_Calendar_ConferenceData();
            $createRequest = new Google_Service_Calendar_CreateConferenceRequest();
            $createRequest->setRequestId(uniqid('meet_', true));
            $createRequest->setConferenceSolutionKey(['type' => 'hangoutsMeet']);
            $conferenceData->setCreateRequest($createRequest);
            $event->setConferenceData($conferenceData);

            // Insert the event
            $calendarId = config('services.google.calendar_id', 'primary');
            $createdEvent = $calendarService->events->insert($calendarId, $event, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'all',
            ]);

            // Extract the Meet link
            $joinUrl = null;
            if ($createdEvent->getConferenceData() && $createdEvent->getConferenceData()->getEntryPoints()) {
                foreach ($createdEvent->getConferenceData()->getEntryPoints() as $entryPoint) {
                    if ($entryPoint->getEntryPointType() === 'video') {
                        $joinUrl = $entryPoint->getUri();
                        break;
                    }
                }
            }

            if (!$joinUrl) {
                throw new \Exception('Failed to generate Google Meet link');
            }

            // Update session with Google event ID and join URL
            $session->google_event_id = $createdEvent->getId();
            $session->join_url = $joinUrl;
            $session->google_meet_link = $joinUrl;
            $session->save();

            Log::info('Google Calendar event created successfully', [
                'session_id' => $session->id,
                'event_id' => $createdEvent->getId(),
                'join_url' => $joinUrl,
            ]);

            return $joinUrl;
        } catch (\Exception $e) {
            Log::error('Failed to create Google Calendar event', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Update a Google Calendar event for a session.
     */
    public function updateSessionEvent(Session $session): string
    {
        if (!$session->google_event_id) {
            return $this->createSessionEvent($session);
        }

        try {
            $tutor = $session->tutor;
            $credentials = $this->getValidCredentials($tutor);
            if (!$credentials) {
                throw new \Exception('Tutor does not have valid Google OAuth credentials');
            }

            $this->googleClient->setAccessToken($credentials->access_token);

            if ($credentials->needsRefresh()) {
                $this->refreshToken($credentials);
            }

            $calendarService = new Google_Service_Calendar($this->googleClient);

            // Get existing event
            $calendarId = config('services.google.calendar_id', 'primary');
            $existingEvent = $calendarService->events->get($calendarId, $session->google_event_id);

            // Update event details
            $existingEvent->setSummary($session->getSessionTitle());
            $existingEvent->setDescription($session->notes ?? 'Music lesson session');

            $startDateTime = new Google_Service_Calendar_EventDateTime();
            $startDateTime->setDateTime($session->start_at_utc->toRfc3339String());
            $startDateTime->setTimeZone($tutor->timezone ?? 'UTC');
            $existingEvent->setStart($startDateTime);

            $endDateTime = new Google_Service_Calendar_EventDateTime();
            $endDateTime->setDateTime($session->end_at_utc->toRfc3339String());
            $endDateTime->setTimeZone($tutor->timezone ?? 'UTC');
            $existingEvent->setEnd($endDateTime);

            // Update attendees
            $attendees = [];
            foreach ($session->getEnrolledStudents() as $student) {
                $attendees[] = ['email' => $student->email];
            }
            $existingEvent->setAttendees($attendees);

            // Update the event
            $updatedEvent = $calendarService->events->update($calendarId, $session->google_event_id, $existingEvent, [
                'sendUpdates' => 'all',
            ]);

            // Extract the Meet link
            $joinUrl = null;
            if ($updatedEvent->getConferenceData() && $updatedEvent->getConferenceData()->getEntryPoints()) {
                foreach ($updatedEvent->getConferenceData()->getEntryPoints() as $entryPoint) {
                    if ($entryPoint->getEntryPointType() === 'video') {
                        $joinUrl = $entryPoint->getUri();
                        break;
                    }
                }
            }

            if ($joinUrl) {
                $session->join_url = $joinUrl;
                $session->google_meet_link = $joinUrl;
                $session->save();
            }

            Log::info('Google Calendar event updated successfully', [
                'session_id' => $session->id,
                'event_id' => $updatedEvent->getId(),
            ]);

            return $joinUrl ?? $session->join_url;
        } catch (\Exception $e) {
            Log::error('Failed to update Google Calendar event', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete a Google Calendar event for a session.
     */
    public function deleteSessionEvent(Session $session): void
    {
        if (!$session->google_event_id) {
            return;
        }

        try {
            $tutor = $session->tutor;
            $credentials = $this->getValidCredentials($tutor);
            if (!$credentials) {
                Log::warning('Cannot delete Google Calendar event - no valid credentials', [
                    'session_id' => $session->id,
                ]);
                return;
            }

            $this->googleClient->setAccessToken($credentials->access_token);

            if ($credentials->needsRefresh()) {
                $this->refreshToken($credentials);
            }

            $calendarService = new Google_Service_Calendar($this->googleClient);
            $calendarId = config('services.google.calendar_id', 'primary');

            $calendarService->events->delete($calendarId, $session->google_event_id, [
                'sendUpdates' => 'all',
            ]);

            // Clear session Google data
            $session->google_event_id = null;
            $session->join_url = null;
            $session->google_meet_link = null;
            $session->save();

            Log::info('Google Calendar event deleted successfully', [
                'session_id' => $session->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete Google Calendar event', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - we don't want to fail session deletion if Google API fails
        }
    }

    /**
     * Test Google Calendar connection for a user.
     */
    public function testConnection(User $user): bool
    {
        try {
            $credentials = $this->getValidCredentials($user);
            if (!$credentials) {
                return false;
            }

            $this->googleClient->setAccessToken($credentials->access_token);

            if ($credentials->needsRefresh()) {
                $this->refreshToken($credentials);
            }

            $calendarService = new Google_Service_Calendar($this->googleClient);
            $calendarId = config('services.google.calendar_id', 'primary');

            // Try to list calendars to test connection
            $calendarService->calendarList->listCalendarList(['maxResults' => 1]);

            return true;
        } catch (\Exception $e) {
            Log::error('Google Calendar connection test failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get valid OAuth credentials for a user.
     */
    private function getValidCredentials(User $user): ?OAuthCredential
    {
        $credential = OAuthCredential::where('user_id', $user->id)
            ->where('provider', 'google')
            ->first();

        if (!$credential || $credential->isExpired()) {
            return null;
        }

        return $credential;
    }

    /**
     * Refresh OAuth token.
     */
    private function refreshToken(OAuthCredential $credentials): void
    {
        try {
            $this->googleClient->refreshToken($credentials->refresh_token);
            $newToken = $this->googleClient->getAccessToken();

            $credentials->access_token = $newToken['access_token'];
            if (isset($newToken['expires_in'])) {
                $credentials->expires_at = now()->addSeconds($newToken['expires_in']);
            }
            $credentials->save();

            Log::info('Google OAuth token refreshed successfully', [
                'user_id' => $credentials->user_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to refresh Google OAuth token', [
                'user_id' => $credentials->user_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}

