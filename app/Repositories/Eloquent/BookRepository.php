<?php

namespace App\Repositories\Eloquent;

use App\Models\Book;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Get all available books.
     *
     * @param int $perPage
     * @param string|null $search
     * @param int|null $genreId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function findAvailableBooks(int $perPage = 12, ?string $search = null, ?int $genreId = null)
    {
        $query = Book::query()->with(['owner', 'genre']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('isbn', 'like', '%' . $search . '%');
            });
        }

        // Apply genre filter
        if ($genreId) {
            $query->where('genre_id', $genreId);
        }

        // Filter by availability and order by latest
        return $query->where('is_available', true)
            ->latest()
            ->paginate($perPage)
            ->appends(['search' => $search, 'genre_id' => $genreId]);
    }

    /**
     * Find a book by its ID.
     *
     * @param int $id
     * @return Book|null
     */
    public function findById(int $id): ?Book
    {
        return Book::with(['owner', 'reviews', 'loans', 'genre'])->find($id);
    }

    /**
     * Create a new book.
     *
     * @param array $data
     * @return Book
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * Update an existing book.
     *
     * @param Book $book
     * @param array $data
     * @return bool
     */
    public function update(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    /**
     * Delete a book.
     *
     * @param Book $book
     * @return bool
     */
    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
