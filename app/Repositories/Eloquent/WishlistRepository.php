<?php

namespace App\Repositories\Eloquent;

use App\Models\Wishlist;
use App\Repositories\Contracts\WishlistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class WishlistRepository implements WishlistRepositoryInterface
{
    /**
     * Find all wishlist items for a specific user.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByUser(int $userId): Collection
    {
        return Wishlist::where('user_id', $userId)
            ->with(['book.owner', 'user'])
            ->latest()
            ->get();
    }

    /**
     * Find a wishlist item by user and book.
     *
     * @param int $userId
     * @param int $bookId
     * @return Wishlist|null
     */
    public function findByUserAndBook(int $userId, int $bookId): ?Wishlist
    {
        return Wishlist::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();
    }

    /**
     * Create a new wishlist item.
     *
     * @param array $data
     * @return Wishlist
     */
    public function create(array $data): Wishlist
    {
        return Wishlist::create($data);
    }

    /**
     * Delete a wishlist item.
     *
     * @param Wishlist $wishlist
     * @return bool
     */
    public function delete(Wishlist $wishlist): bool
    {
        return $wishlist->delete();
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
        return Wishlist::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();
    }
}
