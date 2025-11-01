<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Course Repository Interface
 *
 * Defines the contract for course data access operations.
 */
interface CourseRepositoryInterface
{
    /**
     * Find a course by ID.
     */
    public function find(int $id): ?Course;

    /**
     * Find a course by slug.
     */
    public function findBySlug(string $slug): ?Course;

    /**
     * Find all courses.
     *
     * @return Collection<int, Course>
     */
    public function findAll(): Collection;

    /**
     * Find courses by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Course>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one course by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Course;

    /**
     * Create a new course.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Course;

    /**
     * Update a course.
     *
     * @param array<string, mixed> $data
     */
    public function update(Course $course, array $data): Course;

    /**
     * Delete a course.
     */
    public function delete(Course $course): bool;

    /**
     * Paginate courses.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find courses by teacher.
     *
     * @return Collection<int, Course>
     */
    public function findByTeacher(User $teacher): Collection;

    /**
     * Find published courses.
     *
     * @return Collection<int, Course>
     */
    public function findPublished(): Collection;

    /**
     * Find courses by instrument.
     *
     * @return Collection<int, Course>
     */
    public function findByInstrument(string $instrument, ?int $limit = null): Collection;

    /**
     * Find courses by level.
     *
     * @return Collection<int, Course>
     */
    public function findByLevel(string $level, ?int $limit = null): Collection;

    /**
     * Find courses by category.
     *
     * @return Collection<int, Course>
     */
    public function findByCategory(int $categoryId, ?int $limit = null): Collection;

    /**
     * Search courses.
     *
     * @return Collection<int, Course>
     */
    public function search(string $query, ?int $limit = null): Collection;
}

