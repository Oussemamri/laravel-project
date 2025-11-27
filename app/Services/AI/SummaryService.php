<?php

namespace App\Services\AI;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SummaryService
{
    /**
     * Generate AI summary for a book.
     *
     * @param Book $book
     * @return string|null
     */
    public function generateSummary(Book $book): ?string
    {
        // Cache summary for 7 days
        return Cache::remember(
            "book_summary_{$book->id}",
            60 * 60 * 24 * 7,
            function () use ($book) {
                return $this->callOpenAI($book);
            }
        );
    }

    /**
     * Call OpenAI API to generate summary.
     *
     * @param Book $book
     * @return string|null
     */
    private function callOpenAI(Book $book): ?string
    {
        try {
            $result = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a book expert who creates concise, engaging summaries.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a 3-sentence summary for the book '{$book->title}' by {$book->author}. " .
                                    ($book->description ? "Book description: {$book->description}" : "")
                    ]
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            $summary = $result['choices'][0]['message']['content'] ?? null;
            
            if ($summary) {
                Log::info("AI summary generated for book #{$book->id}");
                return trim($summary);
            }
            
            // Fallback if OpenAI returns nothing
            Log::warning("OpenAI returned no summary for book #{$book->id}");
            return null;

        } catch (\Exception $e) {
            Log::error("Failed to generate AI summary for book #{$book->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear cached summary for a book.
     *
     * @param Book $book
     * @return void
     */
    public function clearCache(Book $book): void
    {
        Cache::forget("book_summary_{$book->id}");
        Log::info("Summary cache cleared for book #{$book->id}");
    }
}
