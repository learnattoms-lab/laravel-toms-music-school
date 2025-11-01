<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\Interfaces\LessonRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Lesson Repository
 *
 * Implementation of LessonRepositoryInterface using Eloquent.
 */
class LessonRepository implements LessonRepositoryInterface
{
    /**
     * Find a lesson by ID.
     */
    public function find(int $id): ?Lesson
    {
        return Lesson::find($id);
    }

    /**
     * Find all lessons.
     *
     * @return Collection<int, Lesson>
     */
    public function findAll(): Collection
    {
        return Lesson::all();
    }

    /**
     * Find lessons by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Lesson>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Lesson::query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('order_index', 'ASC');
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
     * Find one lesson by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Lesson
    {
        return Lesson::where($criteria)->first();
    }

    /**
     * Create a new lesson.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Lesson
    {
        return Lesson::create($data);
    }

    /**
     * Update a lesson.
     *
     * @param array<string, mixed> $data
     */
    public function update(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);
        $lesson->refresh();

        return $lesson;
    }

    /**
     * Delete a lesson.
     */
    public function delete(Lesson $lesson): bool
    {
        return $lesson->delete();
    }

    /**
     * Paginate lessons.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Lesson::query();

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['is_required'])) {
            $query->where('is_required', $filters['is_required']);
        }

        return $query->orderBy('order_index', 'ASC')->paginate($perPage);
    }

    /**
     * Find lessons by course.
     *
     * @return Collection<int, Lesson>
     */
    public function findByCourse(Course $course): Collection
    {
        return Lesson::where('course_id', $course->id)
            ->orderBy('order_index', 'ASC')
            ->get();
    }

    /**
     * Find lessons by course ID.
     *
     * @return Collection<int, Lesson>
     */
    public function findByCourseId(int $courseId): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->orderBy('order_index', 'ASC')
            ->get();
    }

    /**
     * Get lessons ordered by order index for a course.
     *
     * @return Collection<int, Lesson>
     */
    public function getOrderedForCourse(int $courseId): Collection
    {
        return Lesson::where('course_id', $courseId)
            ->orderBy('order_index', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->get();
    }
}

