<?php

namespace App\Repositories\Contracts;

use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;

interface WishlistRepositoryInterface
{
    /**
     * Find all wishlist items for a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByUser(int $userId): Collection;

    /**
     * Find a wishlist item by user and book.
     *
     * @param int $userId
     * @param int $bookId
     * @return Wishlist|null
     */
    public function findByUserAndBook(int $userId, int $bookId): ?Wishlist;

    /**
     * Create a new wishlist item.
     *
     * @param array $data
     * @return Wishlist
     */
    public function create(array $data): Wishlist;

    /**
     * Delete a wishlist item.
     *
     * @param Wishlist $wishlist
     * @return bool
     */
    public function delete(Wishlist $wishlist): bool;

    /**
     * Check if a book is in user's wishlist.
     *
     * @param int $userId
     * @param int $bookId
     * @return bool
     */
    public function isInWishlist(int $userId, int $bookId): bool;
}
