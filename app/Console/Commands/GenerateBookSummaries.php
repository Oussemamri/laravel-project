<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Jobs\GenerateBookSummary;
use Illuminate\Console\Command;

class GenerateBookSummaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:generate-summaries {--book-id= : Generate summary for a specific book}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate AI summaries for books';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookId = $this->option('book-id');

        if ($bookId) {
            $book = Book::find($bookId);
            
            if (!$book) {
                $this->error("Book with ID {$bookId} not found.");
                return 1;
            }

            $this->info("Generating AI summary for book: {$book->title}...");
            GenerateBookSummary::dispatchSync($book);
            
            $book->refresh();
            
            if ($book->ai_summary) {
                $this->info("✓ Summary generated successfully!");
                $this->line("Summary: " . substr($book->ai_summary, 0, 100) . "...");
            } else {
                $this->error("Failed to generate summary.");
            }

        } else {
            $books = Book::whereNull('ai_summary')->get();
            
            if ($books->isEmpty()) {
                $this->info("All books already have AI summaries!");
                return 0;
            }

            $this->info("Generating summaries for {$books->count()} books...");
            
            $bar = $this->output->createProgressBar($books->count());
            $bar->start();

            foreach ($books as $book) {
                GenerateBookSummary::dispatchSync($book);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✓ All summaries generated successfully!");
        }

        return 0;
    }
}
