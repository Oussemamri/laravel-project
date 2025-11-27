<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre;
use App\Repositories\Contracts\GenreRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GenreRepository implements GenreRepositoryInterface
{
    /**
     * Get all genres.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Genre::orderBy('name')->get();
    }

    /**
     * Find a genre by its ID.
     *
     * @param int $id
     * @return Genre|null
     */
    public function findById(int $id): ?Genre
    {
        return Genre::find($id);
    }

    /**
     * Find a genre by its name.
     *
     * @param string $name
     * @return Genre|null
     */
    public function findByName(string $name): ?Genre
    {
        return Genre::where('name', $name)->first();
    }

    /**
     * Create a new genre.
     *
     * @param array $data
     * @return Genre
     */
    public function create(array $data): Genre
    {
        return Genre::create($data);
    }

    /**
     * Update an existing genre.
     *
     * @param Genre $genre
     * @param array $data
     * @return bool
     */
    public function update(Genre $genre, array $data): bool
    {
        return $genre->update($data);
    }

    /**
     * Delete a genre.
     *
     * @param Genre $genre
     * @return bool
     */
    public function delete(Genre $genre): bool
    {
        return $genre->delete();
    }
}
