# BookShare - Final Configuration Steps

## Prerequisites Completed ✅
- [x] Laravel 12 installed
- [x] Laravel Breeze installed
- [x] OpenAI PHP SDK installed
- [x] Backend code copied from project folder
- [x] Database configuration in .env
- [x] Seeder files created

## Step 1: Start XAMPP MySQL

1. Open **XAMPP Control Panel**
2. Click **Start** next to **MySQL**
3. Wait for green "Running" status
4. Click **Start** next to **Apache** (for the web server)

## Step 2: Run Setup Script

Once MySQL is running, execute the setup script:

```powershell
cd C:\Users\ousse\Desktop\bookshare-app
.\setup.ps1
```

This script will:
- ✅ Verify MySQL is running
- ✅ Create `bookshare` database
- ✅ Run all migrations
- ✅ Seed admin user and genres
- ✅ Install npm dependencies
- ✅ Build frontend assets

## Step 3: Register Service Provider

Add `RepositoryServiceProvider` to `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        \App\Providers\RepositoryServiceProvider::class,
    ])
    ->create();
```

## Step 4: Register Routes

Copy the following routes to `routes/web.php` (after the Breeze routes):

```php
<?php

use App\Http\Controllers\Frontend\BookController;
use App\Http\Controllers\Frontend\LoanController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\RecommendationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminLoanController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\GenreController;
use Illuminate\Support\Facades\Route;

// Existing routes...
require __DIR__.'/auth.php';

// Frontend routes (authenticated users)
Route::middleware(['auth', 'verified'])->group(function () {
    // Books
    Route::get('/', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/my-books', [BookController::class, 'myBooks'])->name('books.my-books');

    // Loans
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('/loans/{book}/request', [LoanController::class, 'requestLoan'])->name('loans.request');
    Route::put('/loans/{loan}/accept', [LoanController::class, 'accept'])->name('loans.accept');
    Route::put('/loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::put('/loans/{loan}/return', [LoanController::class, 'markAsReturned'])->name('loans.return');

    // Reviews
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{book}', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{book}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Recommendations
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin Books
    Route::resource('books', AdminBookController::class);
    
    // Admin Loans
    Route::get('/loans', [AdminLoanController::class, 'index'])->name('loans.index');
    Route::put('/loans/{loan}', [AdminLoanController::class, 'update'])->name('loans.update');
    
    // Admin Users
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // Admin Reviews
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Admin Genres
    Route::resource('genres', GenreController::class);
});
```

## Step 5: Configure OpenAI API (Optional)

Update your `.env` file with your OpenAI API key:

```
OPENAI_API_KEY=sk-your-actual-key-here
```

If you don't have an API key yet, the system will use mock data for AI features.

## Step 6: Start Development Server

```powershell
php artisan serve
```

Access the application at: **http://localhost:8000**

## Step 7: Start Queue Worker (Optional - for background AI jobs)

In a separate terminal:

```powershell
cd C:\Users\ousse\Desktop\bookshare-app
php artisan queue:work
```

## Default Credentials

### Admin Account
- Email: `admin@bookshare.com`
- Password: `admin123`

### Test User Account
- Email: `user@bookshare.com`
- Password: `user123`

## Project Structure

```
bookshare-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   └── Frontend/       # User-facing controllers
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/           # Form validation
│   ├── Jobs/                   # Background AI jobs
│   ├── Models/                 # Eloquent models
│   ├── Providers/
│   │   └── RepositoryServiceProvider.php
│   ├── Repositories/           # Repository pattern
│   │   ├── Contracts/         # Interfaces
│   │   └── Eloquent/          # Implementations
│   └── Services/              # Business logic
│       └── AI/                # OpenAI integrations
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Data seeders
└── routes/
    └── web.php                # Application routes

## Features

### User Features
- ✅ User authentication (registration, login, email verification)
- ✅ Add books to share
- ✅ Browse available books
- ✅ Request to borrow books
- ✅ Manage loan requests (accept/reject)
- ✅ Write and manage reviews
- ✅ Wishlist functionality
- ✅ AI-powered book recommendations
- ✅ Auto-generated book summaries

### Admin Features
- ✅ Dashboard with statistics
- ✅ Manage all books
- ✅ Manage all loans
- ✅ Manage users (suspend/activate)
- ✅ Manage reviews (moderate content)
- ✅ Manage genres

### AI Features (requires OpenAI API key)
- ✅ Automatic book summary generation
- ✅ Personalized recommendations based on reading history
- ✅ Review content moderation

## Next Steps

1. **Create Views**: The backend is complete, but you need to create Blade views for:
   - Book listing and detail pages
   - Loan management interface
   - Admin dashboard
   - User profile pages

2. **Customize Design**: Update Tailwind CSS classes in views to match your desired design

3. **Add File Upload**: Implement book cover image upload functionality

4. **Testing**: Test all features with different user roles

5. **Deployment**: Prepare for production deployment when ready

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running in XAMPP
- Check database credentials in `.env`
- Verify `bookshare` database exists

### Migration Errors
- Run: `php artisan migrate:fresh --seed` to reset database
- Check MySQL user permissions

### Queue Jobs Not Running
- Start queue worker: `php artisan queue:work`
- Check `.env` QUEUE_CONNECTION is set to `database` or `redis`

### AI Features Not Working
- Verify OPENAI_API_KEY in `.env`
- System will fallback to mock data if API key is invalid
- Check logs in `storage/logs/laravel.log`

## Support

For issues or questions, refer to:
- `ARCHITECTURE.md` - System architecture details
- `API_DOCUMENTATION.md` - Service and repository documentation
- Laravel Documentation: https://laravel.com/docs/12.x
- OpenAI PHP SDK: https://github.com/openai-php/laravel
