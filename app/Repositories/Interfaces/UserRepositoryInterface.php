<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * User Repository Interface
 *
 * Defines the contract for user data access operations.
 */
interface UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function find(int $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find all users.
     *
     * @return Collection<int, User>
     */
    public function findAll(): Collection;

    /**
     * Find users by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, User>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one user by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?User;

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User;

    /**
     * Update a user.
     *
     * @param array<string, mixed> $data
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     */
    public function delete(User $user): bool;

    /**
     * Paginate users.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find users by role.
     *
     * @return Collection<int, User>
     */
    public function findByRole(string $role): Collection;

    /**
     * Find active users.
     *
     * @return Collection<int, User>
     */
    public function findActive(): Collection;

    /**
     * Find teachers.
     *
     * @return Collection<int, User>
     */
    public function findTeachers(): Collection;

    /**
     * Find students.
     *
     * @return Collection<int, User>
     */
    public function findStudents(): Collection;

    /**
     * Count users by role.
     */
    public function countByRole(string $role): int;

    /**
     * Count active users.
     */
    public function countActive(): int;

    /**
     * Get user statistics.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array;
}

