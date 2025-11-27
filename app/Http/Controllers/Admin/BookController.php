<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * @var BookService
     */
    protected $bookService;

    /**
     * BookController constructor.
     *
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of all books.
     *
     * @return View
     */
    public function index(): View
    {
        $books = \App\Models\Book::with(['owner', 'genre'])
            ->latest()
            ->paginate(20);

        return view('admin.books.index', compact('books'));
    }

    /**
     * Display the specified book.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $book = $this->bookService->findBookById($id);

        if (!$book) {
            abort(404, 'Livre non trouvé');
        }

        return view('admin.books.show', compact('book'));
    }

    /**
     * Remove the specified book (admin override).
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $book = $this->bookService->findBookById($id);
            
            if (!$book) {
                return back()->with('error', 'Livre non trouvé');
            }

            // Admin can delete without ownership check
            $book->delete();

            return redirect()
                ->route('admin.books.index')
                ->with('success', 'Livre supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}
