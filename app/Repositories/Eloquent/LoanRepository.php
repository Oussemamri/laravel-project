<?php

namespace App\Repositories\Eloquent;

use App\Models\Loan;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LoanRepository implements LoanRepositoryInterface
{
    /**
     * Find all loans for a specific user as borrower.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByBorrower(int $userId): Collection
    {
        return Loan::where('borrower_id', $userId)
            ->with(['book.owner', 'borrower'])
            ->latest()
            ->get();
    }

    /**
     * Find all loans for books owned by a specific user.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function findByOwner(int $ownerId): Collection
    {
        return Loan::whereHas('book', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        })
            ->with(['book', 'borrower'])
            ->latest()
            ->get();
    }

    /**
     * Find loans by status.
     *
     * @param string $status
     * @return Collection
     */
    public function findByStatus(string $status): Collection
    {
        return Loan::where('status', $status)
            ->with(['book.owner', 'borrower'])
            ->latest()
            ->get();
    }

    /**
     * Find a loan by its ID.
     *
     * @param int $id
     * @return Loan|null
     */
    public function findById(int $id): ?Loan
    {
        return Loan::with(['book.owner', 'borrower'])->find($id);
    }

    /**
     * Create a new loan.
     *
     * @param array $data
     * @return Loan
     */
    public function create(array $data): Loan
    {
        return Loan::create($data);
    }

    /**
     * Update an existing loan.
     *
     * @param Loan $loan
     * @param array $data
     * @return bool
     */
    public function update(Loan $loan, array $data): bool
    {
        return $loan->update($data);
    }

    /**
     * Delete a loan.
     *
     * @param Loan $loan
     * @return bool
     */
    public function delete(Loan $loan): bool
    {
        return $loan->delete();
    }

    /**
     * Get pending loans for a specific book owner.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function getPendingLoansForOwner(int $ownerId): Collection
    {
        return Loan::where('status', 'pending')
            ->whereHas('book', function ($query) use ($ownerId) {
                $query->where('owner_id', $ownerId);
            })
            ->with(['book', 'borrower'])
            ->latest()
            ->get();
    }
}
