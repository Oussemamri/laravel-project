<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Roman',
            'Science-Fiction',
            'Fantasy',
            'Thriller',
            'Policier',
            'Romance',
            'Historique',
            'Biographie',
            'Essai',
            'Poésie',
            'Théâtre',
            'BD/Comics',
            'Manga',
            'Jeunesse',
            'Développement Personnel',
            'Sciences',
            'Informatique',
            'Art',
            'Cuisine',
            'Voyage',
        ];

        foreach ($genres as $genreName) {
            Genre::firstOrCreate(
                ['name' => $genreName],
                ['slug' => \Illuminate\Support\Str::slug($genreName)]
            );
        }

        $this->command->info(count($genres) . ' genres created successfully!');
    }
}
