<?php

namespace App\Services\AI;

use App\Models\Review;
use Illuminate\Support\Facades\Log;

class ModerationService
{
    /**
     * Moderate review content using AI.
     *
     * @param Review $review
     * @return array ['is_appropriate' => bool, 'reason' => string|null]
     */
    public function moderateReview(Review $review): array
    {
        try {
            // OpenAI integration will be here
            // Example implementation (uncomment when OpenAI is configured):
            /*
            $result = \OpenAI\Laravel\Facades\OpenAI::moderations()->create([
                'input' => $review->comment ?? '',
            ]);

            $flagged = $result['results'][0]['flagged'] ?? false;
            $categories = $result['results'][0]['categories'] ?? [];

            if ($flagged) {
                $flaggedCategories = array_keys(array_filter($categories));
                $reason = 'Inappropriate content detected: ' . implode(', ', $flaggedCategories);
                
                Log::warning("Review #{$review->id} flagged by AI: {$reason}");
                
                return [
                    'is_appropriate' => false,
                    'reason' => $reason
                ];
            }
            */

            // Alternative: Use GPT for content analysis
            /*
            $result = \OpenAI\Laravel\Facades\OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a content moderator. Analyze if the following review contains inappropriate, hateful, or offensive content. Respond with "APPROPRIATE" or "INAPPROPRIATE: [reason]".'
                    ],
                    [
                        'role' => 'user',
                        'content' => $review->comment ?? ''
                    ]
                ],
                'max_tokens' => 100,
                'temperature' => 0.3,
            ]);

            $response = $result['choices'][0]['message']['content'] ?? '';

            if (str_starts_with(strtoupper($response), 'INAPPROPRIATE')) {
                $reason = str_replace('INAPPROPRIATE:', '', strtoupper($response));
                
                Log::warning("Review #{$review->id} flagged by AI: {$reason}");
                
                return [
                    'is_appropriate' => false,
                    'reason' => trim($reason)
                ];
            }
            */

            // Fallback: Basic keyword filtering when OpenAI not configured
            Log::info("AI moderation skipped (OpenAI not configured) for review #{$review->id}");
            return $this->basicModeration($review);

        } catch (\Exception $e) {
            Log::error("Failed to moderate review #{$review->id}: " . $e->getMessage());
            
            // Fallback to basic moderation on error
            return $this->basicModeration($review);
        }
    }

    /**
     * Basic keyword-based moderation (fallback).
     *
     * @param Review $review
     * @return array
     */
    private function basicModeration(Review $review): array
    {
        $comment = strtolower($review->comment ?? '');
        
        // Basic inappropriate word list (extend as needed)
        $inappropriateWords = [
            'spam', 'hate', 'offensive', 'scam', 'stupid', 'idiot',
            // Add more words as needed
        ];

        foreach ($inappropriateWords as $word) {
            if (str_contains($comment, $word)) {
                Log::info("Review #{$review->id} flagged by basic filter: contains '{$word}'");
                
                return [
                    'is_appropriate' => false,
                    'reason' => "Contains potentially inappropriate content"
                ];
            }
        }

        return [
            'is_appropriate' => true,
            'reason' => null
        ];
    }

    /**
     * Moderate and flag review if inappropriate.
     *
     * @param Review $review
     * @return bool Returns true if review was flagged
     */
    public function moderateAndFlag(Review $review): bool
    {
        $result = $this->moderateReview($review);

        if (!$result['is_appropriate']) {
            $review->update(['is_flagged' => true]);
            
            Log::warning("Review #{$review->id} has been flagged: {$result['reason']}");
            
            return true;
        }

        return false;
    }

    /**
     * Batch moderate multiple reviews.
     *
     * @param \Illuminate\Support\Collection $reviews
     * @return array ['flagged' => int, 'checked' => int]
     */
    public function moderateReviews($reviews): array
    {
        $flagged = 0;
        $checked = 0;

        foreach ($reviews as $review) {
            if ($this->moderateAndFlag($review)) {
                $flagged++;
            }
            $checked++;
        }

        Log::info("Batch moderation complete: {$flagged} flagged out of {$checked} reviews");

        return [
            'flagged' => $flagged,
            'checked' => $checked
        ];
    }
}
