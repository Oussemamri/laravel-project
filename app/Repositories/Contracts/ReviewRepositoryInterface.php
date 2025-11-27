<?php

namespace App\Repositories\Contracts;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

interface ReviewRepositoryInterface
{
    /**
     * Find all reviews for a specific book.
     *
     * @param int $bookId
     * @return Collection
     */
    public function findByBook(int $bookId): Collection;

    /**
     * Find all reviews by a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByUser(int $userId): Collection;

    /**
     * Find a review by its ID.
     *
     * @param int $id
     * @return Review|null
     */
    public function findById(int $id): ?Review;

    /**
     * Create a new review.
     *
     * @param array $data
     * @return Review
     */
    public function create(array $data): Review;

    /**
     * Update an existing review.
     *
     * @param Review $review
     * @param array $data
     * @return bool
     */
    public function update(Review $review, array $data): bool;

    /**
     * Delete a review.
     *
     * @param Review $review
     * @return bool
     */
    public function delete(Review $review): bool;

    /**
     * Get average rating for a book.
     *
     * @param int $bookId
     * @return float
     */
    public function getAverageRating(int $bookId): float;

    /**
     * Find flagged reviews.
     *
     * @return Collection
     */
    public function findFlaggedReviews(): Collection;
}
