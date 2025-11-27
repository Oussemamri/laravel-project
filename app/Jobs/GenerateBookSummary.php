<?php

namespace App\Jobs;

use App\Models\Book;
use App\Services\AI\SummaryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateBookSummary implements ShouldQueue
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
    public $backoff = 10;

    /**
     * The book instance.
     *
     * @var Book
     */
    protected $book;

    /**
     * Create a new job instance.
     *
     * @param Book $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job.
     *
     * @param SummaryService $summaryService
     * @return void
     */
    public function handle(SummaryService $summaryService): void
    {
        try {
            Log::info("Starting AI summary generation for book #{$this->book->id}");

            // Generate AI summary
            $summary = $summaryService->generateSummary($this->book);

            if ($summary) {
                // Update book with AI summary
                $this->book->update(['ai_summary' => $summary]);
                
                Log::info("AI summary successfully generated for book #{$this->book->id}");
            } else {
                Log::warning("AI summary generation returned null for book #{$this->book->id}");
            }

        } catch (\Exception $e) {
            Log::error("Failed to generate AI summary for book #{$this->book->id}: " . $e->getMessage());
            
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
        Log::error("Job failed after {$this->tries} attempts for book #{$this->book->id}: " . $exception->getMessage());
        
        // You could send notification to admin here
        // Notification::send($admin, new JobFailedNotification($this->book, $exception));
    }
}
