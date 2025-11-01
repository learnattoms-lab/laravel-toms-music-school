<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Course Controller
 *
 * Handles API operations for courses.
 */
class CourseController extends Controller
{
    private CourseRepositoryInterface $courseRepository;
    private EnrollmentRepositoryInterface $enrollmentRepository;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        EnrollmentRepositoryInterface $enrollmentRepository
    ) {
        $this->courseRepository = $courseRepository;
        $this->enrollmentRepository = $enrollmentRepository;
    }

    /**
     * Display a listing of courses.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['status', 'published', 'teacher_id', 'instrument', 'level', 'category_id', 'search']);
            $perPage = (int) $request->get('per_page', 15);

            $courses = $this->courseRepository->paginate($perPage, $filters);

            return response()->json([
                'data' => CourseResource::collection($courses->items()),
                'meta' => [
                    'current_page' => $courses->currentPage(),
                    'last_page' => $courses->lastPage(),
                    'per_page' => $courses->perPage(),
                    'total' => $courses->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list courses', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve courses',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created course.
     */
    public function store(StoreCourseRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Generate slug from title
            $slug = Str::slug($validated['title']);
            
            // Ensure slug is unique
            $baseSlug = $slug;
            $counter = 1;
            while ($this->courseRepository->findBySlug($slug)) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $courseData = array_merge($validated, [
                'slug' => $slug,
                'teacher_id' => $request->user()->id,
                'status' => $validated['status'] ?? 'draft',
            ]);

            // Set published_at if status is published
            if ($courseData['status'] === 'published') {
                $courseData['published_at'] = now();
            }

            $course = $this->courseRepository->create($courseData);

            Log::info('Course created', [
                'course_id' => $course->id,
                'teacher_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Course created successfully',
                'data' => new CourseResource($course),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create course', [
                'error' => $e->getMessage(),
                'teacher_id' => $request->user()->id,
            ]);

            return response()->json([
                'message' => 'Failed to create course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified course.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseRepository->find($id);

            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            // Load relationships
            $course->load(['teacher', 'category', 'coverFile']);

            // Check if user is enrolled (if authenticated)
            $isEnrolled = false;
            if ($request->user()) {
                $isEnrolled = $this->enrollmentRepository->isEnrolled($request->user(), $course);
            }

            return response()->json([
                'data' => new CourseResource($course),
                'is_enrolled' => $isEnrolled,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve course', [
                'error' => $e->getMessage(),
                'course_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified course.
     */
    public function update(UpdateCourseRequest $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseRepository->find($id);

            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            $validated = $request->validated();

            // Generate new slug if title is updated
            if (isset($validated['title'])) {
                $slug = Str::slug($validated['title']);
                
                // Ensure slug is unique (excluding current course)
                $baseSlug = $slug;
                $counter = 1;
                while (($existing = $this->courseRepository->findBySlug($slug)) && $existing->id !== $course->id) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $validated['slug'] = $slug;
            }

            // Set published_at if status is changed to published
            if (isset($validated['status']) && $validated['status'] === 'published' && !$course->isPublished()) {
                $validated['published_at'] = now();
            }

            $course = $this->courseRepository->update($course, $validated);

            Log::info('Course updated', [
                'course_id' => $course->id,
            ]);

            return response()->json([
                'message' => 'Course updated successfully',
                'data' => new CourseResource($course),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update course', [
                'error' => $e->getMessage(),
                'course_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to update course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified course.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseRepository->find($id);

            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            // Check if user is the course owner
            if ($request->user()->id !== $course->teacher_id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete your own courses.',
                ], 403);
            }

            $this->courseRepository->delete($course);

            Log::info('Course deleted', [
                'course_id' => $course->id,
            ]);

            return response()->json([
                'message' => 'Course deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete course', [
                'error' => $e->getMessage(),
                'course_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to delete course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enroll in a course.
     */
    public function enroll(Request $request, int $id): JsonResponse
    {
        try {
            $course = $this->courseRepository->find($id);

            if (!$course) {
                return response()->json([
                    'message' => 'Course not found',
                ], 404);
            }

            // Check if already enrolled
            if ($this->enrollmentRepository->isEnrolled($request->user(), $course)) {
                return response()->json([
                    'message' => 'You are already enrolled in this course',
                ], 422);
            }

            // Create enrollment
            $enrollment = $this->enrollmentRepository->create([
                'student_id' => $request->user()->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'status' => 'active',
                'progress_pct' => 0,
            ]);

            Log::info('User enrolled in course', [
                'user_id' => $request->user()->id,
                'course_id' => $course->id,
            ]);

            return response()->json([
                'message' => 'Successfully enrolled in course',
                'data' => $enrollment,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to enroll in course', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id,
                'course_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to enroll in course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

