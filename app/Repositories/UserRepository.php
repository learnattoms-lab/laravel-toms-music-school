<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * User Repository
 *
 * Implementation of UserRepositoryInterface using Eloquent.
 */
class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Find all users.
     *
     * @return Collection<int, User>
     */
    public function findAll(): Collection
    {
        return User::all();
    }

    /**
     * Find users by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, User>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = User::query();

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
     * Find one user by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?User
    {
        return User::where($criteria)->first();
    }

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     *
     * @param array<string, mixed> $data
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);
        $user->refresh();

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Paginate users.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = User::query();

        if (isset($filters['role'])) {
            $query->whereJsonContains('roles', $filters['role']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_teacher'])) {
            $query->where('is_teacher', $filters['is_teacher']);
        }

        if (isset($filters['email_verified'])) {
            $query->where('email_verified', $filters['email_verified']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find users by role.
     *
     * @return Collection<int, User>
     */
    public function findByRole(string $role): Collection
    {
        return User::whereJsonContains('roles', $role)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find active users.
     *
     * @return Collection<int, User>
     */
    public function findActive(): Collection
    {
        return User::active()->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Find teachers.
     *
     * @return Collection<int, User>
     */
    public function findTeachers(): Collection
    {
        return User::teachers()->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Find students.
     *
     * @return Collection<int, User>
     */
    public function findStudents(): Collection
    {
        return User::students()->orderBy('created_at', 'DESC')->get();
    }

    /**
     * Count users by role.
     */
    public function countByRole(string $role): int
    {
        return User::whereJsonContains('roles', $role)->count();
    }

    /**
     * Count active users.
     */
    public function countActive(): int
    {
        return User::active()->count();
    }

    /**
     * Get user statistics.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(): array
    {
        $totalUsers = User::count();
        $activeUsers = $this->countActive();
        $teachers = $this->countByRole('ROLE_TEACHER');
        $students = User::students()->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'teachers' => $teachers,
            'students' => $students,
            'inactive_users' => $totalUsers - $activeUsers,
        ];
    }
}

