<?php

namespace App\Jobs;

use App\Models\Review;
use App\Services\AI\ModerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ModerateReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * The review instance.
     *
     * @var Review
     */
    protected $review;

    /**
     * Create a new job instance.
     *
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Execute the job.
     *
     * @param ModerationService $moderationService
     * @return void
     */
    public function handle(ModerationService $moderationService): void
    {
        try {
            Log::info("Starting AI moderation for review #{$this->review->id}");

            // Moderate the review content
            $result = $moderationService->moderateReview($this->review);

            if (!$result['is_appropriate']) {
                // Flag inappropriate review
                $this->review->update(['is_flagged' => true]);
                
                Log::warning("Review #{$this->review->id} has been flagged: {$result['reason']}");
                
                // You could send notification to admin here
                // Notification::send($admin, new ReviewFlaggedNotification($this->review, $result['reason']));
                
            } else {
                Log::info("Review #{$this->review->id} passed AI moderation");
            }

        } catch (\Exception $e) {
            Log::error("Failed to moderate review #{$this->review->id}: " . $e->getMessage());
            
            // Don't re-throw - we don't want moderation failures to block review posting
            // Just log the error and continue
            Log::info("Review #{$this->review->id} will remain unflagged due to moderation error");
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
        Log::error("Moderation job failed for review #{$this->review->id}: " . $exception->getMessage());
        
        // Don't flag the review if moderation completely fails
        // This is a safety mechanism to avoid false positives
    }
}
