<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Repositories\Interfaces\QuizRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Quiz Repository
 *
 * Implementation of QuizRepositoryInterface using Eloquent.
 */
class QuizRepository implements QuizRepositoryInterface
{
    /**
     * Find a quiz by ID.
     */
    public function find(int $id): ?Quiz
    {
        return Quiz::find($id);
    }

    /**
     * Find all quizzes.
     *
     * @return Collection<int, Quiz>
     */
    public function findAll(): Collection
    {
        return Quiz::all();
    }

    /**
     * Find quizzes by criteria.
     *
     * @param array<string, mixed> $criteria
     * @return Collection<int, Quiz>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query = Quiz::query();

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
     * Find one quiz by criteria.
     *
     * @param array<string, mixed> $criteria
     */
    public function findOneBy(array $criteria): ?Quiz
    {
        return Quiz::where($criteria)->first();
    }

    /**
     * Create a new quiz.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Quiz
    {
        return Quiz::create($data);
    }

    /**
     * Update a quiz.
     *
     * @param array<string, mixed> $data
     */
    public function update(Quiz $quiz, array $data): Quiz
    {
        $quiz->update($data);
        $quiz->refresh();

        return $quiz;
    }

    /**
     * Delete a quiz.
     */
    public function delete(Quiz $quiz): bool
    {
        return $quiz->delete();
    }

    /**
     * Paginate quizzes.
     *
     * @param array<string, mixed> $filters
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Quiz::query();

        if (isset($filters['lesson_id'])) {
            $query->where('lesson_id', $filters['lesson_id']);
        }

        return $query->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Find quizzes by lesson.
     *
     * @return Collection<int, Quiz>
     */
    public function findByLesson(Lesson $lesson): Collection
    {
        return Quiz::where('lesson_id', $lesson->id)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Find quizzes by lesson ID.
     *
     * @return Collection<int, Quiz>
     */
    public function findByLessonId(int $lessonId): Collection
    {
        return Quiz::where('lesson_id', $lessonId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}

