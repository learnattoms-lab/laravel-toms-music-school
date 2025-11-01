<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssignmentResource;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Assignment Controller
 *
 * Handles API operations for assignments.
 */
class AssignmentController extends Controller
{
    private AssignmentRepositoryInterface $assignmentRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        AssignmentRepositoryInterface $assignmentRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->assignmentRepository = $assignmentRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Display a listing of assignments.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['lesson_id', 'session_id', 'course_id']);
            $perPage = (int) $request->get('per_page', 15);

            $assignments = $this->assignmentRepository->paginate($perPage, $filters);

            return response()->json([
                'data' => AssignmentResource::collection($assignments->items()),
                'meta' => [
                    'current_page' => $assignments->currentPage(),
                    'last_page' => $assignments->lastPage(),
                    'per_page' => $assignments->perPage(),
                    'total' => $assignments->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list assignments', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve assignments',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'course_id' => ['required', 'integer', 'exists:course,id'],
                'lesson_id' => ['nullable', 'integer', 'exists:lesson,id'],
                'session_id' => ['nullable', 'integer', 'exists:session,id'],
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:5000'],
                'instructions_html' => ['nullable', 'string'],
                'due_at' => ['nullable', 'date', 'after:now'],
                'max_points' => ['nullable', 'integer', 'min:0'],
                'attachments' => ['nullable', 'array'],
                'is_required' => ['nullable', 'boolean'],
                'allow_late_submission' => ['nullable', 'boolean'],
                'late_penalty' => ['nullable', 'integer', 'min:0', 'max:100'],
            ]);

            // Verify user owns the course (teacher)
            $course = $this->courseRepository->find($validated['course_id']);
            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'You can only create assignments for your own courses',
                ], 403);
            }

            $assignment = $this->assignmentRepository->create($validated);

            Log::info('Assignment created', [
                'assignment_id' => $assignment->id,
                'course_id' => $validated['course_id'],
            ]);

            return response()->json([
                'message' => 'Assignment created successfully',
                'data' => new AssignmentResource($assignment),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create assignment', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to create assignment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified assignment.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = $this->assignmentRepository->find($id);

            if (!$assignment) {
                return response()->json([
                    'message' => 'Assignment not found',
                ], 404);
            }

            return response()->json([
                'data' => new AssignmentResource($assignment),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve assignment', [
                'error' => $e->getMessage(),
                'assignment_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve assignment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified assignment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = $this->assignmentRepository->find($id);

            if (!$assignment) {
                return response()->json([
                    'message' => 'Assignment not found',
                ], 404);
            }

            // Verify user owns the course
            $course = $this->courseRepository->find($assignment->course_id);
            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update assignments for your own courses.',
                ], 403);
            }

            $validated = $request->validate([
                'title' => ['sometimes', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:5000'],
                'instructions_html' => ['nullable', 'string'],
                'due_at' => ['nullable', 'date'],
                'max_points' => ['nullable', 'integer', 'min:0'],
                'attachments' => ['nullable', 'array'],
                'is_required' => ['nullable', 'boolean'],
                'allow_late_submission' => ['nullable', 'boolean'],
                'late_penalty' => ['nullable', 'integer', 'min:0', 'max:100'],
            ]);

            $assignment = $this->assignmentRepository->update($assignment, $validated);

            return response()->json([
                'message' => 'Assignment updated successfully',
                'data' => new AssignmentResource($assignment),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update assignment', [
                'error' => $e->getMessage(),
                'assignment_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to update assignment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified assignment.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = $this->assignmentRepository->find($id);

            if (!$assignment) {
                return response()->json([
                    'message' => 'Assignment not found',
                ], 404);
            }

            // Verify user owns the course
            $course = $this->courseRepository->find($assignment->course_id);
            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete assignments for your own courses.',
                ], 403);
            }

            $this->assignmentRepository->delete($assignment);

            return response()->json([
                'message' => 'Assignment deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete assignment', [
                'error' => $e->getMessage(),
                'assignment_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to delete assignment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit an assignment (for students).
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        try {
            $assignment = $this->assignmentRepository->find($id);

            if (!$assignment) {
                return response()->json([
                    'message' => 'Assignment not found',
                ], 404);
            }

            $validated = $request->validate([
                'content' => ['required', 'string'],
                'attachments' => ['nullable', 'array'],
                'attachments.*' => ['integer', 'exists:stored_file,id'],
            ]);

            // TODO: Create AssignmentSubmission model and repository
            // This will be implemented when we create AssignmentSubmissionController

            return response()->json([
                'message' => 'Assignment submitted successfully',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to submit assignment', [
                'error' => $e->getMessage(),
                'assignment_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to submit assignment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

