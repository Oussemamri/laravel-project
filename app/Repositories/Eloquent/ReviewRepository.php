<?php

namespace App\Repositories\Eloquent;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository implements ReviewRepositoryInterface
{
    /**
     * Find all reviews for a specific book.
     *
     * @param int $bookId
     * @return Collection
     */
    public function findByBook(int $bookId): Collection
    {
        return Review::where('book_id', $bookId)
            ->with(['user', 'book'])
            ->latest()
            ->get();
    }

    /**
     * Find all reviews by a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByUser(int $userId): Collection
    {
        return Review::where('user_id', $userId)
            ->with(['book', 'user'])
            ->latest()
            ->get();
    }

    /**
     * Find a review by its ID.
     *
     * @param int $id
     * @return Review|null
     */
    public function findById(int $id): ?Review
    {
        return Review::with(['book', 'user'])->find($id);
    }

    /**
     * Create a new review.
     *
     * @param array $data
     * @return Review
     */
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    /**
     * Update an existing review.
     *
     * @param Review $review
     * @param array $data
     * @return bool
     */
    public function update(Review $review, array $data): bool
    {
        return $review->update($data);
    }

    /**
     * Delete a review.
     *
     * @param Review $review
     * @return bool
     */
    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Get average rating for a book.
     *
     * @param int $bookId
     * @return float
     */
    public function getAverageRating(int $bookId): float
    {
        return (float) Review::where('book_id', $bookId)
            ->avg('rating') ?? 0.0;
    }

    /**
     * Find flagged reviews.
     *
     * @return Collection
     */
    public function findFlaggedReviews(): Collection
    {
        return Review::where('is_flagged', true)
            ->with(['book', 'user'])
            ->latest()
            ->get();
    }
}
