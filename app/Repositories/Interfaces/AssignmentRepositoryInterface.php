<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Assignment;
use App\Models\Lesson;
use App\Models\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Assignment Repository Interface
 *
 * Defines the contract for assignment data access operations.
 */
interface AssignmentRepositoryInterface
{
    /**
     * Find an assignment by ID.
     */
    public function find(int $id): ?Assignment;

    /**
     * Find all assignments.
     *
     * @return Collection<int, Assignment>
     */
    public function findAll(): Collection;

    /**
     * Find assignments by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Assignment>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one assignment by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Assignment;

    /**
     * Create a new assignment.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Assignment;

    /**
     * Update an assignment.
     *
     * @param array<string, mixed> $data
     */
    public function update(Assignment $assignment, array $data): Assignment;

    /**
     * Delete an assignment.
     */
    public function delete(Assignment $assignment): bool;

    /**
     * Paginate assignments.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find assignments by lesson.
     *
     * @return Collection<int, Assignment>
     */
    public function findByLesson(Lesson $lesson): Collection;

    /**
     * Find assignments by session.
     *
     * @return Collection<int, Assignment>
     */
    public function findBySession(Session $session): Collection;
}

