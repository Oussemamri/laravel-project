<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Services\GenreService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenreController extends Controller
{
    /**
     * @var GenreService
     */
    protected $genreService;

    /**
     * GenreController constructor.
     *
     * @param GenreService $genreService
     */
    public function __construct(GenreService $genreService)
    {
        $this->genreService = $genreService;
    }

    /**
     * Display a listing of genres.
     *
     * @return View
     */
    public function index(): View
    {
        $genres = $this->genreService->getAllGenres();

        return view('admin.genres.index', compact('genres'));
    }

    /**
     * Show the form for creating a new genre.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.genres.create');
    }

    /**
     * Store a newly created genre.
     *
     * @param StoreGenreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreGenreRequest $request): RedirectResponse
    {
        try {
            $genre = $this->genreService->createGenre($request->validated());

            return redirect()
                ->route('admin.genres.index')
                ->with('success', 'Genre créé avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified genre.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $genre = $this->genreService->findGenreById($id);

        if (!$genre) {
            abort(404, 'Genre non trouvé');
        }

        return view('admin.genres.edit', compact('genre'));
    }

    /**
     * Update the specified genre.
     *
     * @param UpdateGenreRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateGenreRequest $request, int $id): RedirectResponse
    {
        try {
            $this->genreService->updateGenre($id, $request->validated());

            return redirect()
                ->route('admin.genres.index')
                ->with('success', 'Genre mis à jour avec succès!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified genre.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->genreService->deleteGenre($id);

            return redirect()
                ->route('admin.genres.index')
                ->with('success', 'Genre supprimé avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
