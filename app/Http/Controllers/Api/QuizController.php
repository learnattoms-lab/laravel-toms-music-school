<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizResource;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Quiz Controller
 *
 * Handles API operations for quizzes.
 */
class QuizController extends Controller
{
    private QuizRepositoryInterface $quizRepository;
    private LessonRepositoryInterface $lessonRepository;
    private CourseRepositoryInterface $courseRepository;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        LessonRepositoryInterface $lessonRepository,
        CourseRepositoryInterface $courseRepository
    ) {
        $this->quizRepository = $quizRepository;
        $this->lessonRepository = $lessonRepository;
        $this->courseRepository = $courseRepository;
    }

    /**
     * Display a listing of quizzes.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['lesson_id']);
            $perPage = (int) $request->get('per_page', 15);

            $quizzes = $this->quizRepository->paginate($perPage, $filters);

            return response()->json([
                'data' => QuizResource::collection($quizzes->items()),
                'meta' => [
                    'current_page' => $quizzes->currentPage(),
                    'last_page' => $quizzes->lastPage(),
                    'per_page' => $quizzes->perPage(),
                    'total' => $quizzes->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to list quizzes', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to retrieve quizzes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created quiz.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'lesson_id' => ['required', 'integer', 'exists:lesson,id'],
                'questions' => ['required', 'array', 'min:1'],
                'questions.*.question' => ['required', 'string'],
                'questions.*.type' => ['required', 'string', 'in:multiple_choice,true_false,short_answer'],
                'questions.*.options' => ['required_if:questions.*.type,multiple_choice', 'array'],
                'questions.*.correct_answer' => ['required', 'string'],
                'questions.*.points' => ['required', 'integer', 'min:1'],
                'pass_mark' => ['nullable', 'integer', 'min:0', 'max:100'],
                'instructions' => ['nullable', 'string', 'max:5000'],
                'time_limit' => ['nullable', 'integer', 'min:1'], // minutes
                'allow_retakes' => ['nullable', 'boolean'],
                'max_attempts' => ['nullable', 'integer', 'min:1'],
                'shuffle_questions' => ['nullable', 'boolean'],
                'show_correct_answers' => ['nullable', 'boolean'],
            ]);

            // Verify user owns the course (via lesson)
            $lesson = $this->lessonRepository->find($validated['lesson_id']);
            if (!$lesson) {
                return response()->json([
                    'message' => 'Lesson not found',
                ], 404);
            }

            $course = $this->courseRepository->find($lesson->course_id);
            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'You can only create quizzes for your own courses',
                ], 403);
            }

            $quiz = $this->quizRepository->create($validated);

            Log::info('Quiz created', [
                'quiz_id' => $quiz->id,
                'lesson_id' => $validated['lesson_id'],
            ]);

            return response()->json([
                'message' => 'Quiz created successfully',
                'data' => new QuizResource($quiz),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create quiz', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Failed to create quiz',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified quiz.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($id);

            if (!$quiz) {
                return response()->json([
                    'message' => 'Quiz not found',
                ], 404);
            }

            // Hide correct answers if quiz settings don't allow showing them
            $quizData = new QuizResource($quiz);

            return response()->json([
                'data' => $quizData,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve quiz', [
                'error' => $e->getMessage(),
                'quiz_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to retrieve quiz',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified quiz.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($id);

            if (!$quiz) {
                return response()->json([
                    'message' => 'Quiz not found',
                ], 404);
            }

            // Verify user owns the course
            $lesson = $this->lessonRepository->find($quiz->lesson_id);
            $course = $this->courseRepository->find($lesson->course_id);

            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only update quizzes for your own courses.',
                ], 403);
            }

            $validated = $request->validate([
                'questions' => ['sometimes', 'array', 'min:1'],
                'questions.*.question' => ['required_with:questions', 'string'],
                'questions.*.type' => ['required_with:questions', 'string', 'in:multiple_choice,true_false,short_answer'],
                'questions.*.options' => ['required_if:questions.*.type,multiple_choice', 'array'],
                'questions.*.correct_answer' => ['required_with:questions', 'string'],
                'questions.*.points' => ['required_with:questions', 'integer', 'min:1'],
                'pass_mark' => ['nullable', 'integer', 'min:0', 'max:100'],
                'instructions' => ['nullable', 'string', 'max:5000'],
                'time_limit' => ['nullable', 'integer', 'min:1'],
                'allow_retakes' => ['nullable', 'boolean'],
                'max_attempts' => ['nullable', 'integer', 'min:1'],
                'shuffle_questions' => ['nullable', 'boolean'],
                'show_correct_answers' => ['nullable', 'boolean'],
            ]);

            $quiz = $this->quizRepository->update($quiz, $validated);

            return response()->json([
                'message' => 'Quiz updated successfully',
                'data' => new QuizResource($quiz),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update quiz', [
                'error' => $e->getMessage(),
                'quiz_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to update quiz',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified quiz.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($id);

            if (!$quiz) {
                return response()->json([
                    'message' => 'Quiz not found',
                ], 404);
            }

            // Verify user owns the course
            $lesson = $this->lessonRepository->find($quiz->lesson_id);
            $course = $this->courseRepository->find($lesson->course_id);

            if (!$course || $course->teacher_id !== $request->user()->id) {
                return response()->json([
                    'message' => 'Unauthorized. You can only delete quizzes for your own courses.',
                ], 403);
            }

            $this->quizRepository->delete($quiz);

            return response()->json([
                'message' => 'Quiz deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete quiz', [
                'error' => $e->getMessage(),
                'quiz_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to delete quiz',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start a quiz attempt (for students).
     */
    public function attempt(Request $request, int $id): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($id);

            if (!$quiz) {
                return response()->json([
                    'message' => 'Quiz not found',
                ], 404);
            }

            // TODO: Check if user can attempt quiz (enrolled, not exceeded max attempts, etc.)
            // TODO: Create QuizAttempt record
            // This will be implemented when we create QuizAttemptController

            return response()->json([
                'message' => 'Quiz attempt started',
                'quiz' => new QuizResource($quiz),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to start quiz attempt', [
                'error' => $e->getMessage(),
                'quiz_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to start quiz attempt',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit a quiz attempt (for students).
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        try {
            $quiz = $this->quizRepository->find($id);

            if (!$quiz) {
                return response()->json([
                    'message' => 'Quiz not found',
                ], 404);
            }

            $validated = $request->validate([
                'attempt_id' => ['required', 'integer'],
                'answers' => ['required', 'array'],
            ]);

            // TODO: Validate and score quiz attempt
            // TODO: Update QuizAttempt record
            // This will be implemented when we create QuizAttemptController

            return response()->json([
                'message' => 'Quiz submitted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit quiz', [
                'error' => $e->getMessage(),
                'quiz_id' => $id,
            ]);

            return response()->json([
                'message' => 'Failed to submit quiz',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

