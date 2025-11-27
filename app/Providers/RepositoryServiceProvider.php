<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Eloquent Implementations
        $this->app->bind(
            \App\Repositories\Contracts\BookRepositoryInterface::class,
            \App\Repositories\Eloquent\BookRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\LoanRepositoryInterface::class,
            \App\Repositories\Eloquent\LoanRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ReviewRepositoryInterface::class,
            \App\Repositories\Eloquent\ReviewRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\WishlistRepositoryInterface::class,
            \App\Repositories\Eloquent\WishlistRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\GenreRepositoryInterface::class,
            \App\Repositories\Eloquent\GenreRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
