<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    /**
     * @var LoanService
     */
    protected $loanService;

    /**
     * LoanController constructor.
     *
     * @param LoanService $loanService
     */
    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    /**
     * Display a listing of user's loans.
     *
     * @return View
     */
    public function index(): View
    {
        $borrowedLoans = $this->loanService->getUserLoans(auth()->id());
        $ownerLoans = $this->loanService->getOwnerLoans(auth()->id());

        return view('frontend.loans.index', compact('borrowedLoans', 'ownerLoans'));
    }

    /**
     * Request a loan for a book.
     *
     * @param int $book
     * @return RedirectResponse
     */
    public function requestLoan(int $book): RedirectResponse
    {
        try {
            $loan = $this->loanService->requestLoan(
                $book,
                auth()->id()
            );

            return redirect()
                ->route('books.show', $book)
                ->with('success', 'Demande d\'emprunt envoyée avec succès! Le propriétaire sera notifié.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store a loan request.
     *
     * @param StoreLoanRequest $request
     * @return RedirectResponse
     */
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        try {
            $loan = $this->loanService->requestLoan(
                $request->validated()['book_id'],
                auth()->id()
            );

            return back()->with('success', 'Demande d\'emprunt envoyée avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accept/Approve a loan request.
     *
     * @param int $loan
     * @return RedirectResponse
     */
    public function accept(int $loan): RedirectResponse
    {
        try {
            $this->loanService->approveLoan($loan, auth()->id());

            return back()->with('success', 'Emprunt approuvé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a loan request.
     *
     * @param int $loan
     * @return RedirectResponse
     */
    public function reject(int $loan): RedirectResponse
    {
        try {
            $this->loanService->rejectLoan($loan, auth()->id());

            return back()->with('success', 'Emprunt refusé.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mark a loan as returned.
     *
     * @param int $loan
     * @return RedirectResponse
     */
    public function markAsReturned(int $loan): RedirectResponse
    {
        try {
            $this->loanService->returnBook($loan, auth()->id());

            return back()->with('success', 'Livre marqué comme retourné!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display pending loan requests for owner.
     *
     * @return View
     */
    public function pending(): View
    {
        $pendingLoans = $this->loanService->getPendingLoans(auth()->id());

        return view('frontend.loans.pending', compact('pendingLoans'));
    }
}
