<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\Session;
use App\Repositories\Interfaces\AssignmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Assignment Repository
 *
 * Implementation of AssignmentRepositoryInterface using Eloquent.
 */
class AssignmentRepository implements AssignmentRepositoryInterface
{
    /**
     * Find an assignment by ID.
     */
    public function find(int $id): ?Assignment
    {
        return Assignment::find($id);
    }

    /**
     * Find all assignments.
     *
     * @return Collection<int, Assignment>
     */
    public function findAll(): Collection
    {
        return Assignment::all();
    }

    /**
     * Find assignments by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Assignment>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Assignment::query();

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
     * Find one assignment by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Assignment
    {
        return Assignment::where($criteria)->first();
    }

    /**
     * Create a new assignment.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Assignment
    {
        return Assignment::create($data);
    }

    /**
     * Update an assignment.
     *
     * @param array<string, mixed> $data
     */
    public function update(Assignment $assignment, array $data): Assignment
    {
        $assignment->update($data);
        $assignment->refresh();

        return $assignment;
    }

    /**
     * Delete an assignment.
     */
    public function delete(Assignment $assignment): bool
    {
        return $assignment->delete();
    }

    /**
     * Paginate assignments.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Assignment::query();

        if (isset($filters['lesson_id'])) {
            $query->where('lesson_id', $filters['lesson_id']);
        }

        if (isset($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find assignments by lesson.
     *
     * @return Collection<int, Assignment>
     */
    public function findByLesson(Lesson $lesson): Collection
    {
        return Assignment::where('lesson_id', $lesson->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find assignments by session.
     *
     * @return Collection<int, Assignment>
     */
    public function findBySession(Session $session): Collection
    {
        return Assignment::where('session_id', $session->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}

