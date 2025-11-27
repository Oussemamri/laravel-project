<?php

namespace App\Services\AI;

use App\Models\User;
use App\Repositories\Contracts\BookRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RecommendationService
{
    /**
     * @var BookRepositoryInterface
     */
    protected $bookRepository;

    /**
     * RecommendationService constructor.
     *
     * @param BookRepositoryInterface $bookRepository
     */
    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Get AI-powered book recommendations for a user.
     *
     * @param User $user
     * @return array
     */
    public function getRecommendations(User $user): array
    {
        // Cache recommendations for 1 hour
        return Cache::remember(
            "user_recommendations_{$user->id}",
            3600,
            fn() => $this->generateRecommendations($user)
        );
    }

    /**
     * Generate recommendations using AI.
     *
     * @param User $user
     * @return array
     */
    private function generateRecommendations(User $user): array
    {
        try {
            // Get user's reading history
            $history = $user->loans()
                ->where('status', 'returned')
                ->with('book')
                ->latest()
                ->take(10)
                ->get()
                ->pluck('book.title')
                ->filter()
                ->toArray();

            if (empty($history)) {
                // Return popular books if no history
                return $this->getPopularBooks();
            }

            $historyText = implode(', ', $history);

            // OpenAI integration will be here
            // Example implementation (uncomment when OpenAI is configured):
            /*
            $result = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a book recommendation expert. Provide recommendations in JSON format with title and author.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Based on these books the user has read: {$historyText}, recommend 5 similar books. " .
                                    "Format: [{\"title\": \"Book Title\", \"author\": \"Author Name\", \"reason\": \"Brief reason\"}]"
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.8,
            ]);

            $content = $result['choices'][0]['message']['content'] ?? null;
            
            if ($content) {
                Log::info("AI recommendations generated for user #{$user->id}");
                return $this->parseRecommendations($content);
            }
            */

            // Fallback for testing without OpenAI
            Log::info("AI recommendations skipped (OpenAI not configured) for user #{$user->id}");
            return $this->getMockRecommendations($history);

        } catch (\Exception $e) {
            Log::error("Failed to generate AI recommendations for user #{$user->id}: " . $e->getMessage());
            return $this->getPopularBooks();
        }
    }

    /**
     * Parse AI response into structured recommendations.
     *
     * @param string $content
     * @return array
     */
    private function parseRecommendations(string $content): array
    {
        try {
            // Try to parse JSON response
            $recommendations = json_decode($content, true);
            
            if (is_array($recommendations)) {
                return $recommendations;
            }

            // Fallback: parse text format
            return $this->parseTextRecommendations($content);

        } catch (\Exception $e) {
            Log::error("Failed to parse AI recommendations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Parse text-format recommendations.
     *
     * @param string $content
     * @return array
     */
    private function parseTextRecommendations(string $content): array
    {
        $recommendations = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            if (preg_match('/(.+?)\s+by\s+(.+)/i', $line, $matches)) {
                $recommendations[] = [
                    'title' => trim($matches[1]),
                    'author' => trim($matches[2]),
                    'reason' => 'Based on your reading history'
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Get popular books as fallback.
     *
     * @return array
     */
    private function getPopularBooks(): array
    {
        $books = $this->bookRepository->findAvailableBooks()->take(5);

        return $books->map(function ($book) {
            return [
                'title' => $book->title,
                'author' => $book->author,
                'reason' => 'Popular book in our library',
                'book_id' => $book->id
            ];
        })->toArray();
    }

    /**
     * Get mock recommendations for testing.
     *
     * @param array $history
     * @return array
     */
    private function getMockRecommendations(array $history): array
    {
        return [
            [
                'title' => 'The Midnight Library',
                'author' => 'Matt Haig',
                'reason' => 'Based on your interest in thought-provoking fiction'
            ],
            [
                'title' => 'Atomic Habits',
                'author' => 'James Clear',
                'reason' => 'Popular self-improvement book'
            ],
            [
                'title' => 'Project Hail Mary',
                'author' => 'Andy Weir',
                'reason' => 'Science fiction recommendation'
            ],
        ];
    }

    /**
     * Clear recommendation cache for a user.
     *
     * @param User $user
     * @return void
     */
    public function clearCache(User $user): void
    {
        Cache::forget("user_recommendations_{$user->id}");
        Log::info("Recommendation cache cleared for user #{$user->id}");
    }
}
