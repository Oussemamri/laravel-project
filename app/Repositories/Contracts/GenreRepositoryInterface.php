<?php

namespace App\Repositories\Contracts;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

interface GenreRepositoryInterface
{
    /**
     * Get all genres.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a genre by its ID.
     *
     * @param int $id
     * @return Genre|null
     */
    public function findById(int $id): ?Genre;

    /**
     * Find a genre by its name.
     *
     * @param string $name
     * @return Genre|null
     */
    public function findByName(string $name): ?Genre;

    /**
     * Create a new genre.
     *
     * @param array $data
     * @return Genre
     */
    public function create(array $data): Genre;

    /**
     * Update an existing genre.
     *
     * @param Genre $genre
     * @param array $data
     * @return bool
     */
    public function update(Genre $genre, array $data): bool;

    /**
     * Delete a genre.
     *
     * @param Genre $genre
     * @return bool
     */
    public function delete(Genre $genre): bool;
}
