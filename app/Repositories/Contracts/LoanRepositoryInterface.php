<?php

namespace App\Repositories\Contracts;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;

interface LoanRepositoryInterface
{
    /**
     * Find all loans for a specific user as borrower.
     *
     * @param int $userId
     * @return Collection
     */
    public function findByBorrower(int $userId): Collection;

    /**
     * Find all loans for books owned by a specific user.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function findByOwner(int $ownerId): Collection;

    /**
     * Find loans by status.
     *
     * @param string $status
     * @return Collection
     */
    public function findByStatus(string $status): Collection;

    /**
     * Find a loan by its ID.
     *
     * @param int $id
     * @return Loan|null
     */
    public function findById(int $id): ?Loan;

    /**
     * Create a new loan.
     *
     * @param array $data
     * @return Loan
     */
    public function create(array $data): Loan;

    /**
     * Update an existing loan.
     *
     * @param Loan $loan
     * @param array $data
     * @return bool
     */
    public function update(Loan $loan, array $data): bool;

    /**
     * Delete a loan.
     *
     * @param Loan $loan
     * @return bool
     */
    public function delete(Loan $loan): bool;

    /**
     * Get pending loans for a specific book owner.
     *
     * @param int $ownerId
     * @return Collection
     */
    public function getPendingLoansForOwner(int $ownerId): Collection;
}
