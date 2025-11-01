<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Quiz Repository Interface
 *
 * Defines the contract for quiz data access operations.
 */
interface QuizRepositoryInterface
{
    /**
     * Find a quiz by ID.
     */
    public function find(int $id): ?Quiz;

    /**
     * Find all quizzes.
     *
     * @return Collection<int, Quiz>
     */
    public function findAll(): Collection;

    /**
     * Find quizzes by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Quiz>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one quiz by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Quiz;

    /**
     * Create a new quiz.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Quiz;

    /**
     * Update a quiz.
     *
     * @param array<string, mixed> $data
     */
    public function update(Quiz $quiz, array $data): Quiz;

    /**
     * Delete a quiz.
     */
    public function delete(Quiz $quiz): bool;

    /**
     * Paginate quizzes.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find quizzes by lesson.
     *
     * @return Collection<int, Quiz>
     */
    public function findByLesson(Lesson $lesson): Collection;

    /**
     * Find quizzes by lesson ID.
     *
     * @return Collection<int, Quiz>
     */
    public function findByLessonId(int $lessonId): Collection;
}

