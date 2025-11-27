<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWishlistRequest;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * @var WishlistService
     */
    protected $wishlistService;

    /**
     * WishlistController constructor.
     *
     * @param WishlistService $wishlistService
     */
    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    /**
     * Display user's wishlist.
     *
     * @return View
     */
    public function index(): View
    {
        $wishlist = $this->wishlistService->getUserWishlist(auth()->id());

        return view('frontend.wishlist.index', compact('wishlist'));
    }

    /**
     * Add a book to wishlist.
     *
     * @param StoreWishlistRequest $request
     * @return RedirectResponse
     */
    public function store(StoreWishlistRequest $request): RedirectResponse
    {
        try {
            $this->wishlistService->addToWishlist(
                auth()->id(),
                $request->validated()['book_id']
            );

            return back()->with('success', 'Livre ajouté à votre liste de souhaits!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove a book from wishlist.
     *
     * @param int $bookId
     * @return RedirectResponse
     */
    public function destroy(int $bookId): RedirectResponse
    {
        try {
            $this->wishlistService->removeFromWishlist(auth()->id(), $bookId);

            return back()->with('success', 'Livre retiré de votre liste de souhaits!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
