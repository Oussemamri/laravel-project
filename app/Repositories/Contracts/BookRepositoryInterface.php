<?php

namespace App\Repositories\Contracts;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

interface BookRepositoryInterface
{
    /**
     * Get all available books.
     *
     * @param int $perPage
     * @param string|null $search
     * @param int|null $genreId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findAvailableBooks(int $perPage = 12, ?string $search = null, ?int $genreId = null);

    /**
     * Find a book by its ID.
     *
     * @param int $id
     * @return Book|null
     */
    public function findById(int $id): ?Book;

    /**
     * Create a new book.
     *
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book;

    /**
     * Update an existing book.
     *
     * @param Book $book
     * @param array $data
     * @return bool
     */
    public function update(Book $book, array $data): bool;

    /**
     * Delete a book.
     *
     * @param Book $book
     * @return bool
     */
    public function delete(Book $book): bool;
}
