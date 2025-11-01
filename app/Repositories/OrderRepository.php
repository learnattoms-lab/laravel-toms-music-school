<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Order Repository
 *
 * Implementation of OrderRepositoryInterface using Eloquent.
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * Find an order by ID.
     */
    public function find(int $id): ?Order
    {
        return Order::find($id);
    }

    /**
     * Find an order by Stripe checkout session ID.
     */
    public function findByStripeSessionId(string $sessionId): ?Order
    {
        return Order::where('stripe_checkout_session_id', $sessionId)->first();
    }

    /**
     * Find all orders.
     *
     * @return Collection<int, Order>
     */
    public function findAll(): Collection
    {
        return Order::all();
    }

    /**
     * Find orders by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Order>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Order::query();

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
     * Find one order by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Order
    {
        return Order::where($criteria)->first();
    }

    /**
     * Create a new order.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Update an order.
     *
     * @param array<string, mixed> $data
     */
    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        $order->refresh();

        return $order;
    }

    /**
     * Delete an order.
     */
    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    /**
     * Paginate orders.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Order::query();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find orders by user.
     *
     * @return Collection<int, Order>
     */
    public function findByUser(User $user): Collection
    {
        return Order::where('user_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find orders by course.
     *
     * @return Collection<int, Order>
     */
    public function findByCourse(Course $course): Collection
    {
        return Order::where('course_id', $course->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find orders by status.
     *
     * @return Collection<int, Order>
     */
    public function findByStatus(string $status): Collection
    {
        return Order::where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Get total revenue.
     */
    public function getTotalRevenue(): float
    {
        return (float) Order::where('status', 'completed')
            ->sum('amount_cents') / 100;
    }

    /**
     * Get revenue by date range.
     */
    public function getRevenueByDateRange(\DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        $start = Carbon::instance($startDate);
        $end = Carbon::instance($endDate);

        return (float) Order::where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount_cents') / 100;
    }
}

