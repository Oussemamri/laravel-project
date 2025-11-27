<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\AI\RecommendationService;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    /**
     * @var RecommendationService
     */
    protected $recommendationService;

    /**
     * RecommendationController constructor.
     *
     * @param RecommendationService $recommendationService
     */
    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display AI-powered book recommendations.
     *
     * @return View
     */
    public function index(): View
    {
        $recommendations = $this->recommendationService->getRecommendations(auth()->user());

        return view('frontend.recommendations.index', compact('recommendations'));
    }
}
