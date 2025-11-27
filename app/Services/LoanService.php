<?php

namespace App\Services;

use App\Models\Loan;
use App\Repositories\Contracts\BookRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanService
{
    /**
     * @var LoanRepositoryInterface
     */
    protected $loanRepository;

    /**
     * @var BookRepositoryInterface
     */
    protected $bookRepository;

    /**
     * LoanService constructor.
     *
     * @param LoanRepositoryInterface $loanRepository
     * @param BookRepositoryInterface $bookRepository
     */
    public function __construct(
        LoanRepositoryInterface $loanRepository,
        BookRepositoryInterface $bookRepository
    ) {
        $this->loanRepository = $loanRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * Request a loan for a book.
     *
     * @param int $bookId
     * @param int $userId
     * @return Loan
     * @throws \Exception
     */
    public function requestLoan(int $bookId, int $userId): Loan
    {
        return DB::transaction(function () use ($bookId, $userId) {
            $book = $this->bookRepository->findById($bookId);

            if (!$book) {
                throw new \Exception('Book not found');
            }

            // Business validation
            if (!$book->is_available) {
                throw new \Exception('Book is not available for loan');
            }

            if ($book->owner_id === $userId) {
                throw new \Exception('You cannot borrow your own book');
            }

            // Create loan
            $loan = $this->loanRepository->create([
                'book_id' => $bookId,
                'borrower_id' => $userId,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            // Update book availability
            $this->bookRepository->update($book, ['is_available' => false]);

            Log::info("Loan requested: Book #{$bookId} by User #{$userId}");

            return $loan;
        });
    }

    /**
     * Approve a loan request.
     *
     * @param int $loanId
     * @param int $ownerId
     * @return Loan
     * @throws \Exception
     */
    public function approveLoan(int $loanId, int $ownerId): Loan
    {
        return DB::transaction(function () use ($loanId, $ownerId) {
            $loan = $this->loanRepository->findById($loanId);

            if (!$loan) {
                throw new \Exception('Loan not found');
            }

            if ($loan->book->owner_id !== $ownerId) {
                throw new \Exception('You are not authorized to approve this loan');
            }

            if ($loan->status !== 'pending') {
                throw new \Exception('Only pending loans can be approved');
            }

            $this->loanRepository->update($loan, [
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            Log::info("Loan approved: Loan #{$loanId} by Owner #{$ownerId}");

            return $loan->fresh();
        });
    }

    /**
     * Reject a loan request.
     *
     * @param int $loanId
     * @param int $ownerId
     * @return Loan
     * @throws \Exception
     */
    public function rejectLoan(int $loanId, int $ownerId): Loan
    {
        return DB::transaction(function () use ($loanId, $ownerId) {
            $loan = $this->loanRepository->findById($loanId);

            if (!$loan) {
                throw new \Exception('Loan not found');
            }

            if ($loan->book->owner_id !== $ownerId) {
                throw new \Exception('You are not authorized to reject this loan');
            }

            if ($loan->status !== 'pending') {
                throw new \Exception('Only pending loans can be rejected');
            }

            $this->loanRepository->update($loan, [
                'status' => 'rejected',
            ]);

            // Make book available again
            $this->bookRepository->update($loan->book, ['is_available' => true]);

            Log::info("Loan rejected: Loan #{$loanId} by Owner #{$ownerId}");

            return $loan->fresh();
        });
    }

    /**
     * Mark a loan as returned.
     *
     * @param int $loanId
     * @param int $userId
     * @return Loan
     * @throws \Exception
     */
    public function returnBook(int $loanId, int $userId): Loan
    {
        return DB::transaction(function () use ($loanId, $userId) {
            $loan = $this->loanRepository->findById($loanId);

            if (!$loan) {
                throw new \Exception('Loan not found');
            }

            // Either borrower or owner can mark as returned
            if ($loan->borrower_id !== $userId && $loan->book->owner_id !== $userId) {
                throw new \Exception('You are not authorized to return this loan');
            }

            if ($loan->status !== 'approved') {
                throw new \Exception('Only approved loans can be returned');
            }

            $this->loanRepository->update($loan, [
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            // Make book available again
            $this->bookRepository->update($loan->book, ['is_available' => true]);

            Log::info("Loan returned: Loan #{$loanId}");

            return $loan->fresh();
        });
    }

    /**
     * Get loans for a borrower.
     *
     * @param int $userId
     * @return Collection
     */
    public function getUserLoans(int $userId): Collection
    {
        return $this->loanRepository->findByBorrower($userId);
    }

    /**
     * Get loans for books owned by a user.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function getOwnerLoans(int $ownerId): Collection
    {
        return $this->loanRepository->findByOwner($ownerId);
    }

    /**
     * Get pending loans for a book owner.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function getPendingLoans(int $ownerId): Collection
    {
        return $this->loanRepository->getPendingLoansForOwner($ownerId);
    }
}
