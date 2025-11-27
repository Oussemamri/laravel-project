<?php

namespace App\Services;

use App\Models\Genre;
use App\Repositories\Contracts\GenreRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GenreService
{
    /**
     * @var GenreRepositoryInterface
     */
    protected $genreRepository;

    /**
     * GenreService constructor.
     *
     * @param GenreRepositoryInterface $genreRepository
     */
    public function __construct(GenreRepositoryInterface $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    /**
     * Get all genres.
     *
     * @return Collection
     */
    public function getAllGenres(): Collection
    {
        return $this->genreRepository->all();
    }

    /**
     * Create a new genre.
     *
     * @param array $data
     * @return Genre
     * @throws \Exception
     */
    public function createGenre(array $data): Genre
    {
        // Check if genre already exists
        $existing = $this->genreRepository->findByName($data['name']);

        if ($existing) {
            throw new \Exception('Genre with this name already exists');
        }

        $genre = $this->genreRepository->create($data);

        Log::info("Genre created: {$genre->name}");

        return $genre;
    }

    /**
     * Update an existing genre.
     *
     * @param int $genreId
     * @param array $data
     * @return Genre
     * @throws \Exception
     */
    public function updateGenre(int $genreId, array $data): Genre
    {
        $genre = $this->genreRepository->findById($genreId);

        if (!$genre) {
            throw new \Exception('Genre not found');
        }

        // Check for duplicate name if name is being updated
        if (isset($data['name']) && $data['name'] !== $genre->name) {
            $existing = $this->genreRepository->findByName($data['name']);
            if ($existing) {
                throw new \Exception('Genre with this name already exists');
            }
        }

        $this->genreRepository->update($genre, $data);

        Log::info("Genre updated: Genre #{$genreId}");

        return $genre->fresh();
    }

    /**
     * Delete a genre.
     *
     * @param int $genreId
     * @return bool
     * @throws \Exception
     */
    public function deleteGenre(int $genreId): bool
    {
        $genre = $this->genreRepository->findById($genreId);

        if (!$genre) {
            throw new \Exception('Genre not found');
        }

        // Check if genre has books
        if ($genre->books()->count() > 0) {
            throw new \Exception('Cannot delete genre with associated books');
        }

        $result = $this->genreRepository->delete($genre);

        Log::info("Genre deleted: Genre #{$genreId}");

        return $result;
    }

    /**
     * Find genre by ID.
     *
     * @param int $genreId
     * @return Genre|null
     */
    public function findGenreById(int $genreId): ?Genre
    {
        return $this->genreRepository->findById($genreId);
    }
}
