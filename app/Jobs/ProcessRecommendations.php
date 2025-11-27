<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AI\RecommendationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessRecommendations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 15;

    /**
     * The user instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @param RecommendationService $recommendationService
     * @return void
     */
    public function handle(RecommendationService $recommendationService): void
    {
        try {
            Log::info("Processing AI recommendations for user #{$this->user->id}");

            // Clear existing cache to force fresh recommendations
            $recommendationService->clearCache($this->user);

            // Generate new recommendations
            $recommendations = $recommendationService->getRecommendations($this->user);

            if (!empty($recommendations)) {
                Log::info("AI recommendations successfully processed for user #{$this->user->id}: " . count($recommendations) . " recommendations");
                
                // Store additional metadata if needed
                Cache::put(
                    "user_recommendations_meta_{$this->user->id}",
                    [
                        'generated_at' => now(),
                        'count' => count($recommendations)
                    ],
                    3600
                );
            } else {
                Log::warning("AI recommendations returned empty for user #{$this->user->id}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to process AI recommendations for user #{$this->user->id}: " . $e->getMessage());
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Recommendation job failed after {$this->tries} attempts for user #{$this->user->id}: " . $exception->getMessage());
        
        // You could send notification to admin or user here
    }
}
