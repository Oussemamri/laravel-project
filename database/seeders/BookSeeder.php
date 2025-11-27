<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@bookshare.com')->first();
        $admin = User::where('email', 'admin@bookshare.com')->first();

        $books = [
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'genre' => 'Roman',
                'description' => 'A classic American novel set in the Jazz Age, exploring themes of wealth, love, and the American Dream.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg',
                'user_id' => $user->id,
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'genre' => 'Roman',
                'description' => 'A gripping tale of racial injustice and childhood innocence in the American South.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780061120084-L.jpg',
                'user_id' => $admin->id,
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'isbn' => '9780451524935',
                'genre' => 'Science-Fiction',
                'description' => 'A dystopian social science fiction novel about totalitarianism and surveillance.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Harry Potter and the Sorcerer\'s Stone',
                'author' => 'J.K. Rowling',
                'isbn' => '9780590353427',
                'genre' => 'Fantasy',
                'description' => 'The first book in the magical Harry Potter series about a young wizard\'s adventures.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780590353427-L.jpg',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'The Hobbit',
                'author' => 'J.R.R. Tolkien',
                'isbn' => '9780547928227',
                'genre' => 'Fantasy',
                'description' => 'A fantasy adventure about Bilbo Baggins and his journey to reclaim treasure from a dragon.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg',
                'user_id' => $user->id,
            ],
            [
                'title' => 'The Da Vinci Code',
                'author' => 'Dan Brown',
                'isbn' => '9780307474278',
                'genre' => 'Thriller',
                'description' => 'A thrilling mystery involving secret societies, religious history, and conspiracy.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780307474278-L.jpg',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'isbn' => '9780141439518',
                'genre' => 'Romance',
                'description' => 'A romantic novel of manners about Elizabeth Bennet and Mr. Darcy.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg',
                'user_id' => $user->id,
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'isbn' => '9780316769174',
                'genre' => 'Roman',
                'description' => 'A story about teenage rebellion and alienation in post-war America.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780316769174-L.jpg',
                'user_id' => $admin->id,
            ],
            [
                'title' => 'The Alchemist',
                'author' => 'Paulo Coelho',
                'isbn' => '9780062315007',
                'genre' => 'Développement Personnel',
                'description' => 'A philosophical novel about following your dreams and finding your destiny.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780062315007-L.jpg',
                'user_id' => $user->id,
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9780132350884',
                'genre' => 'Informatique',
                'description' => 'A handbook of agile software craftsmanship for writing clean, maintainable code.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg',
                'user_id' => $admin->id,
            ],
        ];

        foreach ($books as $bookData) {
            $genre = Genre::where('name', $bookData['genre'])->first();
            
            if ($genre) {
                Book::create([
                    'title' => $bookData['title'],
                    'author' => $bookData['author'],
                    'isbn' => $bookData['isbn'],
                    'genre_id' => $genre->id,
                    'description' => $bookData['description'],
                    'cover_image' => $bookData['cover_image'],
                    'owner_id' => $bookData['user_id'],
                    'is_available' => true,
                ]);
            }
        }

        $this->command->info('10 sample books created successfully!');
    }
}
