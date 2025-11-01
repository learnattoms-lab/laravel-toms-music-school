<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Enrollment Repository Interface
 *
 * Defines the contract for enrollment data access operations.
 */
interface EnrollmentRepositoryInterface
{
    /**
     * Find an enrollment by ID.
     */
    public function find(int $id): ?Enrollment;

    /**
     * Find all enrollments.
     *
     * @return Collection<int, Enrollment>
     */
    public function findAll(): Collection;

    /**
     * Find enrollments by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Enrollment>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one enrollment by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Enrollment;

    /**
     * Create a new enrollment.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Enrollment;

    /**
     * Update an enrollment.
     *
     * @param array<string, mixed> $data
     */
    public function update(Enrollment $enrollment, array $data): Enrollment;

    /**
     * Delete an enrollment.
     */
    public function delete(Enrollment $enrollment): bool;

    /**
     * Paginate enrollments.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find enrollment by student and course.
     */
    public function findByStudentAndCourse(User $student, Course $course): ?Enrollment;

    /**
     * Find enrollments by student.
     *
     * @return Collection<int, Enrollment>
     */
    public function findByStudent(User $student): Collection;

    /**
     * Find enrollments by course.
     *
     * @return Collection<int, Enrollment>
     */
    public function findByCourse(Course $course): Collection;

    /**
     * Check if student is enrolled in course.
     */
    public function isEnrolled(User $student, Course $course): bool;

    /**
     * Get enrollment count for a course.
     */
    public function countByCourse(Course $course): int;

    /**
     * Get enrollment count for a student.
     */
    public function countByStudent(User $student): int;
}

