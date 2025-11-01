<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Order Repository Interface
 *
 * Defines the contract for order data access operations.
 */
interface OrderRepositoryInterface
{
    /**
     * Find an order by ID.
     */
    public function find(int $id): ?Order;

    /**
     * Find an order by Stripe checkout session ID.
     */
    public function findByStripeSessionId(string $sessionId): ?Order;

    /**
     * Find all orders.
     *
     * @return Collection<int, Order>
     */
    public function findAll(): Collection;

    /**
     * Find orders by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Order>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection;

    /**
     * Find one order by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Order;

    /**
     * Create a new order.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Order;

    /**
     * Update an order.
     *
     * @param array<string, mixed> $data
     */
    public function update(Order $order, array $data): Order;

    /**
     * Delete an order.
     */
    public function delete(Order $order): bool;

    /**
     * Paginate orders.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Find orders by user.
     *
     * @return Collection<int, Order>
     */
    public function findByUser(User $user): Collection;

    /**
     * Find orders by course.
     *
     * @return Collection<int, Order>
     */
    public function findByCourse(Course $course): Collection;

    /**
     * Find orders by status.
     *
     * @return Collection<int, Order>
     */
    public function findByStatus(string $status): Collection;

    /**
     * Get total revenue.
     */
    public function getTotalRevenue(): float;

    /**
     * Get revenue by date range.
     */
    public function getRevenueByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): float;
}

