<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * @var ReviewService
     */
    protected $reviewService;

    /**
     * ReviewController constructor.
     *
     * @param ReviewService $reviewService
     */
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    /**
     * Display all flagged reviews.
     *
     * @return View
     */
    public function flagged(): View
    {
        $reviews = $this->reviewService->getFlaggedReviews();

        return view('admin.reviews.flagged', compact('reviews'));
    }

    /**
     * Flag a review (admin action).
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function flag(int $id): RedirectResponse
    {
        try {
            $this->reviewService->flagReview($id);

            return back()->with('success', 'Avis signalé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a review (admin action).
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $review = $this->reviewService->findById($id);
            
            if (!$review) {
                return back()->with('error', 'Avis non trouvé');
            }

            $review->delete();

            return back()->with('success', 'Avis supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
