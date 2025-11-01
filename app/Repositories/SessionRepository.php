<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\Session;
use App\Models\User;
use App\Repositories\Interfaces\SessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Session Repository
 *
 * Implementation of SessionRepositoryInterface using Eloquent.
 */
class SessionRepository implements SessionRepositoryInterface
{
    /**
     * Find a session by ID.
     */
    public function find(int $id): ?Session
    {
        return Session::find($id);
    }

    /**
     * Find all sessions.
     *
     * @return Collection<int, Session>
     */
    public function findAll(): Collection
    {
        return Session::all();
    }

    /**
     * Find sessions by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Session>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Session::query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        if ($orderBy !== null) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('start_at_utc', 'ASC');
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
     * Find one session by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Session
    {
        return Session::where($criteria)->first();
    }

    /**
     * Create a new session.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Session
    {
        return Session::create($data);
    }

    /**
     * Update a session.
     *
     * @param array<string, mixed> $data
     */
    public function update(Session $session, array $data): Session
    {
        $session->update($data);
        $session->refresh();

        return $session;
    }

    /**
     * Delete a session.
     */
    public function delete(Session $session): bool
    {
        return $session->delete();
    }

    /**
     * Paginate sessions.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Session::query();

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['tutor_id'])) {
            $query->where('tutor_id', $filters['tutor_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['upcoming'])) {
            $query->where('start_at_utc', '>', now());
        }

        if (isset($filters['past'])) {
            $query->where('start_at_utc', '<', now());
        }

        return $query->orderBy('start_at_utc', 'ASC')->paginate($perPage);
    }

    /**
     * Find sessions by course.
     *
     * @return Collection<int, Session>
     */
    public function findByCourse(Course $course): Collection
    {
        return Session::where('course_id', $course->id)
            ->orderBy('start_at_utc', 'ASC')
            ->get();
    }

    /**
     * Find sessions by tutor.
     *
     * @return Collection<int, Session>
     */
    public function findByTutor(User $tutor): Collection
    {
        return Session::where('tutor_id', $tutor->id)
            ->orderBy('start_at_utc', 'ASC')
            ->get();
    }

    /**
     * Find sessions by student.
     *
     * @return Collection<int, Session>
     */
    public function findByStudent(User $student): Collection
    {
        return Session::whereHas('students', function ($query) use ($student) {
            $query->where('user_id', $student->id);
        })
            ->orderBy('start_at_utc', 'ASC')
            ->get();
    }

    /**
     * Find upcoming sessions.
     *
     * @return Collection<int, Session>
     */
    public function findUpcoming(?int $limit = null): Collection
    {
        $query = Session::where('start_at_utc', '>', now())
            ->orderBy('start_at_utc', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Find past sessions.
     *
     * @return Collection<int, Session>
     */
    public function findPast(?int $limit = null): Collection
    {
        $query = Session::where('start_at_utc', '<', now())
            ->orderBy('start_at_utc', 'DESC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Find sessions by date range.
     *
     * @return Collection<int, Session>
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection
    {
        $start = Carbon::instance($startDate);
        $end = Carbon::instance($endDate);

        return Session::whereBetween('start_at_utc', [$start, $end])
            ->orderBy('start_at_utc', 'ASC')
            ->get();
    }
}

