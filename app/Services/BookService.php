<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class BookService
{
    /**
     * @var BookRepositoryInterface
     */
    protected $bookRepository;

    /**
     * BookService constructor.
     *
     * @param BookRepositoryInterface $bookRepository
     */
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Get all available books.
     *
     * @param int $perPage
     * @param string|null $search
     * @param int|null $genreId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAvailableBooks(int $perPage = 12, ?string $search = null, ?int $genreId = null)
    {
        return $this->bookRepository->findAvailableBooks($perPage, $search, $genreId);
    }

    /**
     * Create a new book.
     *
     * @param array $data
     * @return Book
     */
    public function createBook(array $data): Book
    {
        // Business logic can be added here (e.g., triggering AI summary job)
        $book = $this->bookRepository->create($data);

        // Example: Dispatch AI summary job here
        // GenerateBookSummary::dispatch($book);

        Log::info("Book created: {$book->title} by user {$book->owner_id}");

        return $book;
    }

    /**
     * Find a book by ID.
     *
     * @param int $id
     * @return Book|null
     */
    public function findBookById(int $id): ?Book
    {
        return $this->bookRepository->findById($id);
    }

    /**
     * Update a book.
     *
     * @param int $id
     * @param array $data
     * @param int $userId
     * @return Book
     * @throws \Exception
     */
    public function updateBook(int $id, array $data, int $userId): Book
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \Exception('Book not found');
        }

        if ($book->owner_id !== $userId) {
            throw new \Exception('You are not authorized to update this book');
        }

        $this->bookRepository->update($book, $data);

        Log::info("Book updated: Book #{$id} by User #{$userId}");

        return $book->fresh();
    }

    /**
     * Delete a book.
     *
     * @param int $id
     * @param int $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteBook(int $id, int $userId): bool
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            throw new \Exception('Book not found');
        }

        if ($book->owner_id !== $userId) {
            throw new \Exception('You are not authorized to delete this book');
        }

        $result = $this->bookRepository->delete($book);

        Log::info("Book deleted: Book #{$id} by User #{$userId}");

        return $result;
    }
}
