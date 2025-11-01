<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lesson Repository Interface
 *
 * Defines the contract for lesson data access operations.
 */
interface LessonRepositoryInterface
{
    /**
     * Find a lesson by ID.
     */
    public function find(int $id): ?Lesson;

    /**
     * Find all lessons.
     *
     * @return Collection<int, Lesson>
     */
    public function findAll(): Collection;

    /**
     * Find lessons by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Lesson>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one lesson by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Lesson;

    /**
     * Create a new lesson.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Lesson;

    /**
     * Update a lesson.
     *
     * @param array<string, mixed> $data
     */
    public function update(Lesson $lesson, array $data): Lesson;

    /**
     * Delete a lesson.
     */
    public function delete(Lesson $lesson): bool;

    /**
     * Paginate lessons.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find lessons by course.
     *
     * @return Collection<int, Lesson>
     */
    public function findByCourse(Course $course): Collection;

    /**
     * Find lessons by course ID.
     *
     * @return Collection<int, Lesson>
     */
    public function findByCourseId(int $courseId): Collection;

    /**
     * Get lessons ordered by order index for a course.
     *
     * @return Collection<int, Lesson>
     */
    public function getOrderedForCourse(int $courseId): Collection;
}

