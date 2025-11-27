<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class WishlistService
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * WishlistService constructor.
     *
     * @param WishlistRepositoryInterface $wishlistRepository
     */
    public function __construct(WishlistRepositoryInterface $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * Add a book to user's wishlist.
     *
     * @param int $userId
     * @param int $bookId
     * @return Wishlist
     * @throws \Exception
     */
    public function addToWishlist(int $userId, int $bookId): Wishlist
    {
        // Check if already in wishlist
        if ($this->wishlistRepository->isInWishlist($userId, $bookId)) {
            throw new \Exception('Book is already in your wishlist');
        }

        $wishlist = $this->wishlistRepository->create([
            'user_id' => $userId,
            'book_id' => $bookId,
        ]);

        Log::info("Book added to wishlist: Book #{$bookId} by User #{$userId}");

        return $wishlist;
    }

    /**
     * Remove a book from user's wishlist.
     *
     * @param int $userId
     * @param int $bookId
     * @return bool
     * @throws \Exception
     */
    public function removeFromWishlist(int $userId, int $bookId): bool
    {
        $wishlist = $this->wishlistRepository->findByUserAndBook($userId, $bookId);

        if (!$wishlist) {
            throw new \Exception('Book is not in your wishlist');
        }

        $result = $this->wishlistRepository->delete($wishlist);

        Log::info("Book removed from wishlist: Book #{$bookId} by User #{$userId}");

        return $result;
    }

    /**
     * Get user's wishlist.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserWishlist(int $userId): Collection
    {
        return $this->wishlistRepository->findByUser($userId);
    }

    /**
     * Check if a book is in user's wishlist.
     *
     * @param int $userId
     * @param int $bookId
     * @return bool
     */
    public function isInWishlist(int $userId, int $bookId): bool
    {
        return $this->wishlistRepository->isInWishlist($userId, $bookId);
    }
}
