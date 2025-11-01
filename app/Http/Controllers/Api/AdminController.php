<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Admin Controller
 *
 * Handles API operations for admin functionality.
 */
class AdminController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private CourseRepositoryInterface $courseRepository;
    private EnrollmentRepositoryInterface $enrollmentRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CourseRepositoryInterface $courseRepository,
        EnrollmentRepositoryInterface $enrollmentRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $enrollmentRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get admin dashboard statistics.
     */
    public function dashboard(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $userStats = $this->userRepository->getStatistics();
            $totalCourses = $this->courseRepository->findAll()->count();
            $totalEnrollments = $this->enrollmentRepository->findAll()->count();
            $totalRevenue = $this->orderRepository->getTotalRevenue();

            $stats = [
                'users' => $userStats,
                'courses' => [
                    'total' => $totalCourses,
                    'published' => $this->courseRepository->findPublished()->count(),
                ],
                'enrollments' => [
                    'total' => $totalEnrollments,
                    'active' => $this->enrollmentRepository->findByStatus('active')->count(),
                ],
                'revenue' => [
                    'total' => $totalRevenue,
                    'this_month' => $this->orderRepository->getRevenueByDateRange(
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ),
                ],
            ];

            return response()->json([
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get admin dashboard', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all users (admin only).
     */
    public function users(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $filters = $request->only(['role', 'is_active', 'is_teacher', 'search']);
            $perPage = (int) $request->get('per_page', 15);

            $users = $this->userRepository->paginate($perPage, $filters);

            return response()->json([
                'data' => UserResource::collection($users->items()),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list users', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all teachers (admin only).
     */
    public function teachers(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $teachers = $this->userRepository->findTeachers();

            return response()->json([
                'data' => UserResource::collection($teachers),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list teachers', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve teachers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all students (admin only).
     */
    public function students(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $students = $this->userRepository->findStudents();

            return response()->json([
                'data' => UserResource::collection($students),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list students', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get analytics data (admin only).
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            // Verify user is admin
            if (!$request->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $timeRange = $request->get('range', '30'); // days
            $startDate = now()->subDays((int) $timeRange);
            $endDate = now();

            $analytics = [
                'users' => [
                    'total' => $this->userRepository->findAll()->count(),
                    'new_this_period' => $this->userRepository->findBy([
                        'created_at' => ['>=' => $startDate],
                    ])->count(),
                ],
                'revenue' => [
                    'total' => $this->orderRepository->getTotalRevenue(),
                    'this_period' => $this->orderRepository->getRevenueByDateRange($startDate, $endDate),
                ],
                'courses' => [
                    'total' => $this->courseRepository->findAll()->count(),
                    'published' => $this->courseRepository->findPublished()->count(),
                ],
                'enrollments' => [
                    'total' => $this->enrollmentRepository->findAll()->count(),
                    'active' => $this->enrollmentRepository->findByStatus('active')->count(),
                ],
            ];

            return response()->json([
                'data' => $analytics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get analytics', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve analytics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

