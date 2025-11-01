<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Repositories\Interfaces\EnrollmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Enrollment Repository
 *
 * Implementation of EnrollmentRepositoryInterface using Eloquent.
 */
class EnrollmentRepository implements EnrollmentRepositoryInterface
{
    /**
     * Find an enrollment by ID.
     */
    public function find(int $id): ?Enrollment
    {
        return Enrollment::find($id);
    }

    /**
     * Find all enrollments.
     *
     * @return Collection<int, Enrollment>
     */
    public function findAll(): Collection
    {
        return Enrollment::all();
    }

    /**
     * Find enrollments by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Enrollment>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Enrollment::query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('enrolled_at', 'DESC');
        }

        if ($offset !== null) {
            $query->offset($offset);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Find one enrollment by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Enrollment
    {
        return Enrollment::where($criteria)->first();
    }

    /**
     * Create a new enrollment.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    /**
     * Update an enrollment.
     *
     * @param array<string, mixed> $data
     */
    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        $enrollment->update($data);
        $enrollment->refresh();

        return $enrollment;
    }

    /**
     * Delete an enrollment.
     */
    public function delete(Enrollment $enrollment): bool
    {
        return $enrollment->delete();
    }

    /**
     * Paginate enrollments.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Enrollment::query();

        if (isset($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('enrolled_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find enrollment by student and course.
     */
    public function findByStudentAndCourse(User $student, Course $course): ?Enrollment
    {
        return Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();
    }

    /**
     * Find enrollments by student.
     *
     * @return Collection<int, Enrollment>
     */
    public function findByStudent(User $student): Collection
    {
        return Enrollment::where('student_id', $student->id)
            ->orderBy('enrolled_at', 'DESC')
            ->get();
    }

    /**
     * Find enrollments by course.
     *
     * @return Collection<int, Enrollment>
     */
    public function findByCourse(Course $course): Collection
    {
        return Enrollment::where('course_id', $course->id)
            ->orderBy('enrolled_at', 'DESC')
            ->get();
    }

    /**
     * Check if student is enrolled in course.
     */
    public function isEnrolled(User $student, Course $course): bool
    {
        return Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Get enrollment count for a course.
     */
    public function countByCourse(Course $course): int
    {
        return Enrollment::where('course_id', $course->id)->count();
    }

    /**
     * Get enrollment count for a student.
     */
    public function countByStudent(User $student): int
    {
        return Enrollment::where('student_id', $student->id)->count();
    }
}

