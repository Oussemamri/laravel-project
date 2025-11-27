<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use App\Services\LoanService;
use App\Services\ReviewService;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * @var BookService
     */
    protected $bookService;

    /**
     * @var LoanService
     */
    protected $loanService;

    /**
     * @var ReviewService
     */
    protected $reviewService;

    /**
     * DashboardController constructor.
     *
     * @param BookService $bookService
     * @param LoanService $loanService
     * @param ReviewService $reviewService
     */
    public function __construct(
        BookService $bookService,
        LoanService $loanService,
        ReviewService $reviewService
    ) {
        $this->bookService = $bookService;
        $this->loanService = $loanService;
        $this->reviewService = $reviewService;
    }

    /**
     * Display admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_books' => \App\Models\Book::count(),
            'available_books' => \App\Models\Book::where('is_available', true)->count(),
            'pending_loans' => \App\Models\Loan::where('status', 'pending')->count(),
            'flagged_reviews' => $this->reviewService->getFlaggedReviews()->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();
        $recentBooks = \App\Models\Book::with('owner')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentBooks'));
    }
}
