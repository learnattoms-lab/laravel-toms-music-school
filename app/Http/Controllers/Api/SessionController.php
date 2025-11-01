<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Session\StoreSessionRequest;
use App\Http\Requests\Session\UpdateSessionRequest;
use App\Http\Resources\SessionResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\SessionRepositoryInterface;
use App\Services\Interfaces\GoogleCalendarServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Session Controller
 *
 * Handles API operations for sessions (live lessons).
 */
class SessionController extends Controller
{
    private SessionRepositoryInterface $sessionRepository;
    private CourseRepositoryInterface $courseRepository;
    private GoogleCalendarServiceInterface $googleCalendarService;

    public function __construct(
        SessionRepositoryInterface $sessionRepository,
        CourseRepositoryInterface $courseRepository,
        GoogleCalendarServiceInterface $googleCalendarService
    ) {
        $this->sessionRepository = $sessionRepository;
        $this->courseRepository = $courseRepository;
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Display a listing of sessions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['course_id', 'tutor_id', 'status', 'upcoming', 'past']);
            $perPage = (int) $request->get('per_page', 15);

            $sessions = $this->sessionRepository->paginate($perPage, $filters);

            return response()->json([
                'data' => SessionResource::collection($sessions->items()),
                'meta' => [
                    'current_page' => $sessions->currentPage(),
                    'last_page' => $sessions->lastPage(),
                    'per_page' => $sessions->perPage(),
                    'total' => $sessions->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list sessions', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve sessions',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created session.
     */
    public function store(StoreSessionRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            // Verify user owns the course
            $course = $this->courseRepository->find($validated['course_id']);
            if (!$course || $course->teacher_id !== $user->id) {
                return response()->json([
                    'message' => 'You can only create sessions for your own courses',
                ], 403);
            }

            $sessionData = array_merge($validated, [
                'tutor_id' => $user->id,
                'status' => $validated['status'] ?? 'scheduled',
            ]);

            // Extract student_ids if provided
            $studentIds = $sessionData['student_ids'] ?? [];
            unset($sessionData['student_ids']);

            // Create session
            $session = $this->sessionRepository->create($sessionData);

            // Attach students if provided
            if (!empty($studentIds)) {
                $session->students()->attach($studentIds);
            }

            // Create Google Calendar event and generate Meet link
            try {
                $joinUrl = $this->googleCalendarService->createSessionEvent($session);
                Log::info('Google Calendar event created for session', [
                    'session_id' => $session->id,
                    'join_url' => $joinUrl,
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to create Google Calendar event', [
                    'session_id' => $session->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue even if Google Calendar fails
            }

            $session->load(['course', 'lesson', 'tutor', 'students']);

            Log::info('Session created', [
                'session_id' => $session->id,
                'tutor_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Session created successfully',
                'data' => new SessionResource($session),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create session', [
                'error' => $e->getMessage(),
                'tutor_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to create session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified session.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $session = $this->sessionRepository->find($id);

            if (!$session) {
                return response()->json([
                    'message' => 'Session not found',
                ], 404);
            }

            $session->load(['course', 'lesson', 'tutor', 'students']);

            return response()->json([
                'data' => new SessionResource($session),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve session', [
                'error' => $e->getMessage(),
                'session_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified session.
     */
    public function update(UpdateSessionRequest $request, int $id): JsonResponse
    {
        try {
            $session = $this->sessionRepository->find($id);

            if (!$session) {
                return response()->json([
                    'message' => 'Session not found',
                ], 404);
            }

            $validated = $request->validated();

            // Extract student_ids if provided
            $studentIds = $validated['student_ids'] ?? null;
            unset($validated['student_ids']);

            // Update session
            $session = $this->sessionRepository->update($session, $validated);

            // Update students if provided
            if ($studentIds !== null) {
                $session->students()->sync($studentIds);
            }

            // Update Google Calendar event if it exists
            if ($session->google_event_id) {
                try {
                    $this->googleCalendarService->updateSessionEvent($session);
                    Log::info('Google Calendar event updated for session', [
                        'session_id' => $session->id,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to update Google Calendar event', [
                        'session_id' => $session->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue even if Google Calendar fails
                }
            }

            $session->load(['course', 'lesson', 'tutor', 'students']);

            Log::info('Session updated', [
                'session_id' => $session->id,
            ]);

            return response()->json([
                'message' => 'Session updated successfully',
                'data' => new SessionResource($session),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update session', [
                'error' => $e->getMessage(),
                'session_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to update session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified session.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $session = $this->sessionRepository->find($id);

            if (!$session) {
                return response()->json([
                    'message' => 'Session not found',
                ], 404);
            }

            // Check if user is the session tutor
            if ($request->user()->id !== $session->tutor_id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete your own sessions.',
                ], 403);
            }

            // Delete Google Calendar event if it exists
            if ($session->google_event_id) {
                try {
                    $this->googleCalendarService->deleteSessionEvent($session);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete Google Calendar event', [
                        'session_id' => $session->id,
                        'error' => $e->getMessage(),
                    ]);
                    // Continue with session deletion even if Google Calendar fails
                }
            }

            $this->sessionRepository->delete($session);

            Log::info('Session deleted', [
                'session_id' => $session->id,
            ]);

            return response()->json([
                'message' => 'Session deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete session', [
                'error' => $e->getMessage(),
                'session_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to delete session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get join link for a session.
     */
    public function join(Request $request, int $id): JsonResponse
    {
        try {
            $session = $this->sessionRepository->find($id);

            if (!$session) {
                return response()->json([
                    'message' => 'Session not found',
                ], 404);
            }

            // Check if user is enrolled in the session or is the tutor
            $user = $request->user();
            $isTutor = $session->tutor_id === $user->id;
            $isEnrolled = $session->students()->where('user_id', $user->id)->exists();

            if (!$isTutor && !$isEnrolled) {
                return response()->json([
                    'message' => 'You must be enrolled in this session to join',
                ], 403);
            }

            // If no join URL exists, try to create Google Calendar event
            if (!$session->join_url && !$session->google_meet_link) {
                try {
                    $joinUrl = $this->googleCalendarService->createSessionEvent($session);
                    $session->refresh();
                } catch (\Exception $e) {
                    Log::warning('Failed to create Google Calendar event for join', [
                        'session_id' => $session->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $joinUrl = $session->join_url ?? $session->google_meet_link;

            if (!$joinUrl) {
                return response()->json([
                    'message' => 'Join link not available. Please contact the tutor.',
                ], 404);
            }

            return response()->json([
                'join_url' => $joinUrl,
                'session' => new SessionResource($session),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get join link', [
                'error' => $e->getMessage(),
                'session_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to get join link',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

