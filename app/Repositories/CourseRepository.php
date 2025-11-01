<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Interfaces\CourseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Course Repository
 *
 * Implementation of CourseRepositoryInterface using Eloquent.
 */
class CourseRepository implements CourseRepositoryInterface
{
    /**
     * Find a course by ID.
     */
    public function find(int $id): ?Course
    {
        return Course::find($id);
    }

    /**
     * Find a course by slug.
     */
    public function findBySlug(string $slug): ?Course
    {
        return Course::where('slug', $slug)->first();
    }

    /**
     * Find all courses.
     *
     * @return Collection<int, Course>
     */
    public function findAll(): Collection
    {
        return Course::all();
    }

    /**
     * Find courses by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Course>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Course::query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'DESC');
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
     * Find one course by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Course
    {
        return Course::where($criteria)->first();
    }

    /**
     * Create a new course.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Course
    {
        return Course::create($data);
    }

    /**
     * Update a course.
     *
     * @param array<string, mixed> $data
     */
    public function update(Course $course, array $data): Course
    {
        $course->update($data);
        $course->refresh();

        return $course;
    }

    /**
     * Delete a course.
     */
    public function delete(Course $course): bool
    {
        return $course->delete();
    }

    /**
     * Paginate courses.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Course::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['published'])) {
            if ($filters['published']) {
                $query->published();
            } else {
                $query->where('status', '!=', 'published');
            }
        }

        if (isset($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }

        if (isset($filters['instrument'])) {
            $query->byInstrument($filters['instrument']);
        }

        if (isset($filters['level'])) {
            $query->byLevel($filters['level']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find courses by teacher.
     *
     * @return Collection<int, Course>
     */
    public function findByTeacher(User $teacher): Collection
    {
        return Course::byTeacher($teacher->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find published courses.
     *
     * @return Collection<int, Course>
     */
    public function findPublished(): Collection
    {
        return Course::published()
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find courses by instrument.
     *
     * @return Collection<int, Course>
     */
    public function findByInstrument(string $instrument, ?int $limit = null): Collection
    {
        $query = Course::byInstrument($instrument)
            ->published()
            ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Find courses by level.
     *
     * @return Collection<int, Course>
     */
    public function findByLevel(string $level, ?int $limit = null): Collection
    {
        $query = Course::byLevel($level)
            ->published()
            ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Find courses by category.
     *
     * @return Collection<int, Course>
     */
    public function findByCategory(int $categoryId, ?int $limit = null): Collection
    {
        $query = Course::where('category_id', $categoryId)
            ->published()
            ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Search courses.
     *
     * @return Collection<int, Course>
     */
    public function search(string $query, ?int $limit = null): Collection
    {
        $queryBuilder = Course::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->orderBy('created_at', 'DESC');

        if ($limit !== null) {
            $queryBuilder->limit($limit);
        }

        return $queryBuilder->get();
    }
}

