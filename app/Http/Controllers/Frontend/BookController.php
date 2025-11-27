<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Jobs\GenerateBookSummary;
use App\Services\BookService;
use App\Services\GenreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * @var BookService
     */
    protected $bookService;

    /**
     * @var GenreService
     */
    protected $genreService;

    /**
     * BookController constructor.
     *
     * @param BookService $bookService
     * @param GenreService $genreService
     */
    public function __construct(BookService $bookService, GenreService $genreService)
    {
        $this->bookService = $bookService;
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of available books.
     *
     * @return View
     */
    public function index(): View
    {
        $search = request('search');
        $genreId = request('genre_id');
        
        $books = $this->bookService->getAvailableBooks(12, $search, $genreId);
        $genres = $this->genreService->getAllGenres();

        return view('frontend.books.index', compact('books', 'genres', 'search', 'genreId'));
    }

    /**
     * Show the form for creating a new book.
     *
     * @return View
     */
    public function create(): View
    {
        $genres = $this->genreService->getAllGenres();
        
        return view('frontend.books.create', compact('genres'));
    }

    /**
     * Store a newly created book in storage.
     *
     * @param StoreBookRequest $request
     * @return RedirectResponse
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['owner_id'] = auth()->id();

            // Handle file upload
            if ($request->hasFile('cover_image_file')) {
                $file = $request->file('cover_image_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('covers', $filename, 'public');
                $data['cover_image'] = asset('storage/' . $path);
            }

            $book = $this->bookService->createBook($data);

            // Dispatch AI summary generation job
            GenerateBookSummary::dispatch($book)->onQueue('ai-tasks');

            return redirect()
                ->route('books.show', $book)
                ->with('success', 'Livre ajouté avec succès! Le résumé IA sera généré sous peu.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout du livre: ' . $e->getMessage());
        }
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

        return view('frontend.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $book = $this->bookService->findBookById($id);

        if (!$book) {
            abort(404, 'Livre non trouvé');
        }

        // Check authorization
        if ($book->owner_id !== auth()->id()) {
            abort(403, 'Non autorisé');
        }

        $genres = $this->genreService->getAllGenres();

        return view('frontend.books.edit', compact('book', 'genres'));
    }

    /**
     * Update the specified book in storage.
     *
     * @param UpdateBookRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateBookRequest $request, int $id): RedirectResponse
    {
        try {
            $book = $this->bookService->findBookById($id);

            if (!$book || $book->owner_id !== auth()->id()) {
                return back()->with('error', 'Non autorisé');
            }

            $data = $request->validated();

            // Handle checkbox (unchecked checkboxes don't send values)
            $data['is_available'] = $request->has('is_available') ? 1 : 0;

            // Handle file upload
            if ($request->hasFile('cover_image_file')) {
                $file = $request->file('cover_image_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('covers', $filename, 'public');
                $data['cover_image'] = asset('storage/' . $path);
            }

            $this->bookService->updateBook($id, $data, auth()->id());

            return redirect()
                ->route('books.show', $book)
                ->with('success', 'Livre mis à jour avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified book from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->bookService->deleteBook($id, auth()->id());

            return redirect()
                ->route('books.index')
                ->with('success', 'Livre supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Display user's own books.
     *
     * @return View
     */
    public function myBooks(): View
    {
        $books = auth()->user()->ownedBooks()->latest()->get();

        return view('frontend.books.my-books', compact('books'));
    }
}
