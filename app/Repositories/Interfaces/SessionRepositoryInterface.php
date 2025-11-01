<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\Session;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Session Repository Interface
 *
 * Defines the contract for session data access operations.
 */
interface SessionRepositoryInterface
{
    /**
     * Find a session by ID.
     */
    public function find(int $id): ?Session;

    /**
     * Find all sessions.
     *
     * @return Collection<int, Session>
     */
    public function findAll(): Collection;

    /**
     * Find sessions by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Session>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one session by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Session;

    /**
     * Create a new session.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Session;

    /**
     * Update a session.
     *
     * @param array<string, mixed> $data
     */
    public function update(Session $session, array $data): Session;

    /**
     * Delete a session.
     */
    public function delete(Session $session): bool;

    /**
     * Paginate sessions.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find sessions by course.
     *
     * @return Collection<int, Session>
     */
    public function findByCourse(Course $course): Collection;

    /**
     * Find sessions by tutor.
     *
     * @return Collection<int, Session>
     */
    public function findByTutor(User $tutor): Collection;

    /**
     * Find sessions by student.
     *
     * @return Collection<int, Session>
     */
    public function findByStudent(User $student): Collection;

    /**
     * Find upcoming sessions.
     *
     * @return Collection<int, Session>
     */
    public function findUpcoming(?int $limit = null): Collection;

    /**
     * Find past sessions.
     *
     * @return Collection<int, Session>
     */
    public function findPast(?int $limit = null): Collection;

    /**
     * Find sessions by date range.
     *
     * @return Collection<int, Session>
     */
    public function findByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): Collection;
}

