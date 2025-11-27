<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    /**
     * @var ReviewRepositoryInterface
     */
    protected $reviewRepository;

    /**
     * ReviewService constructor.
     *
     * @param ReviewRepositoryInterface $reviewRepository
     */
    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * Create a new review.
     *
     * @param array $data
     * @return Review
     * @throws \Exception
     */
    public function createReview(array $data): Review
    {
        // Business validation: Check if user has already reviewed this book
        $existingReview = $this->reviewRepository->findByBook($data['book_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existingReview) {
            throw new \Exception('You have already reviewed this book');
        }

        $review = $this->reviewRepository->create($data);

        // Dispatch AI moderation job here if needed
        // ModerateReview::dispatch($review);

        Log::info("Review created: Book #{$data['book_id']} by User #{$data['user_id']}");

        return $review;
    }

    /**
     * Update an existing review.
     *
     * @param int $reviewId
     * @param array $data
     * @param int $userId
     * @return Review
     * @throws \Exception
     */
    public function updateReview(int $reviewId, array $data, int $userId): Review
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            throw new \Exception('Review not found');
        }

        if ($review->user_id !== $userId) {
            throw new \Exception('You are not authorized to update this review');
        }

        $this->reviewRepository->update($review, $data);

        Log::info("Review updated: Review #{$reviewId}");

        return $review->fresh();
    }

    /**
     * Delete a review.
     *
     * @param int $reviewId
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteReview(int $reviewId, int $userId): bool
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            throw new \Exception('Review not found');
        }

        if ($review->user_id !== $userId) {
            throw new \Exception('You are not authorized to delete this review');
        }

        $result = $this->reviewRepository->delete($review);

        Log::info("Review deleted: Review #{$reviewId}");

        return $result;
    }

    /**
     * Get reviews for a specific book.
     *
     * @param int $bookId
     * @return Collection
     */
    public function getBookReviews(int $bookId): Collection
    {
        return $this->reviewRepository->findByBook($bookId);
    }

    /**
     * Get reviews by a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserReviews(int $userId): Collection
    {
        return $this->reviewRepository->findByUser($userId);
    }

    /**
     * Get average rating for a book.
     *
     * @param int $bookId
     * @return float
     */
    public function getAverageRating(int $bookId): float
    {
        return $this->reviewRepository->getAverageRating($bookId);
    }

    /**
     * Flag a review (admin action).
     *
     * @param int $reviewId
     * @return Review
     * @throws \Exception
     */
    public function flagReview(int $reviewId): Review
    {
        $review = $this->reviewRepository->findById($reviewId);

        if (!$review) {
            throw new \Exception('Review not found');
        }

        $this->reviewRepository->update($review, ['is_flagged' => true]);

        Log::info("Review flagged: Review #{$reviewId}");

        return $review->fresh();
    }

    /**
     * Get all flagged reviews.
     *
     * @return Collection
     */
    public function getFlaggedReviews(): Collection
    {
        return $this->reviewRepository->findFlaggedReviews();
    }

    /**
     * Find review by ID.
     *
     * @param int $reviewId
     * @return Review|null
     */
    public function findById(int $reviewId): ?Review
    {
        return $this->reviewRepository->findById($reviewId);
    }
}
