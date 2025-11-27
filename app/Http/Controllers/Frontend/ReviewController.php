<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Jobs\ModerateReview;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

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
     * Store a newly created review.
     *
     * @param StoreReviewRequest $request
     * @return RedirectResponse
     */
    public function store(StoreReviewRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $review = $this->reviewService->createReview($data);

            // Dispatch AI moderation job
            ModerateReview::dispatch($review)->onQueue('ai-tasks');

            return back()->with('success', 'Avis ajouté avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified review.
     *
     * @param UpdateReviewRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateReviewRequest $request, int $id): RedirectResponse
    {
        try {
            $this->reviewService->updateReview($id, $request->validated(), auth()->id());

            return back()->with('success', 'Avis mis à jour avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified review.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->reviewService->deleteReview($id, auth()->id());

            return back()->with('success', 'Avis supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
