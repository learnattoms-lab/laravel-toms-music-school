<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * User Controller
 *
 * Handles API operations for user profiles and dashboard.
 */
class UserController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private EnrollmentRepositoryInterface $enrollmentRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EnrollmentRepositoryInterface $enrollmentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->enrollmentRepository = $enrollmentRepository;
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update authenticated user profile.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $validated = $request->validated();

            $user = $this->userRepository->update($user, $validated);

            Log::info('User profile updated', [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user profile', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user dashboard data.
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Get enrolled courses
            $enrollments = $this->enrollmentRepository->findByStudent($user);

            // Get upcoming sessions (if enrolled)
            $upcomingSessions = collect();
            foreach ($enrollments as $enrollment) {
                $courseSessions = $enrollment->course->sessions()->upcoming()->limit(5)->get();
                $upcomingSessions = $upcomingSessions->merge($courseSessions);
            }

            // Get recent assignments
            // TODO: Implement when AssignmentSubmission model is complete

            // Get progress statistics
            $stats = [
                'total_courses' => $enrollments->count(),
                'completed_courses' => $enrollments->where('status', 'completed')->count(),
                'total_lessons' => $user->total_lessons,
                'completed_lessons' => $user->completed_lessons,
                'practice_hours' => $user->practice_hours,
                'experience_points' => $user->experience_points,
                'level' => $user->level,
            ];

            return response()->json([
                'user' => new UserResource($user),
                'enrollments' => $enrollments,
                'upcoming_sessions' => $upcomingSessions->take(10)->values(),
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get dashboard data', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user progress.
     */
    public function progress(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $progress = [
                'total_courses' => $this->enrollmentRepository->countByStudent($user),
                'completed_courses' => $this->enrollmentRepository->findByStudent($user)
                    ->where('status', 'completed')
                    ->count(),
                'total_lessons' => $user->total_lessons,
                'completed_lessons' => $user->completed_lessons,
                'practice_hours' => $user->practice_hours,
                'experience_points' => $user->experience_points,
                'level' => $user->level,
                'achievements' => $user->achievements ?? [],
                'badges' => $user->badges ?? [],
            ];

            return response()->json([
                'data' => $progress,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user progress', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve progress',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user achievements.
     */
    public function achievements(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'achievements' => $user->achievements ?? [],
                'badges' => $user->badges ?? [],
                'experience_points' => $user->experience_points,
                'level' => $user->level,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get user achievements', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve achievements',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

