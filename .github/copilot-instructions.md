# BookShare AI Coding Agent Instructions

## Architecture Overview

BookShare is a Laravel 12 community book-sharing platform using **Repository Pattern** with service layer architecture:

- **Controllers** (`app/Http/Controllers/{Frontend,Admin}/`): Thin layer handling HTTP logic, delegates to Services
- **Services** (`app/Services/`): Business logic layer (BookService, LoanService, ReviewService, etc.)
- **Repositories** (`app/Repositories/Eloquent/`): Data access layer implementing interfaces from `Contracts/`
- **Jobs** (`app/Jobs/`): Async tasks (GenerateBookSummary, ModerateReview, ProcessRecommendations) - queue-based with retry logic
- **AI Services** (`app/Services/AI/`): OpenAI integration for summaries, moderation, recommendations (currently stubbed with placeholders)

**Key Pattern**: All repository interfaces must be bound in `app/Providers/RepositoryServiceProvider.php` and registered in `bootstrap/app.php`.

## Critical Workflows

### Development Setup (XAMPP + Windows)
```powershell
# Start XAMPP MySQL first, then run:
.\setup.ps1  # Creates DB, runs migrations, seeds data, builds assets
composer run dev  # Starts Laravel server, queue worker, logs, and Vite concurrently
```

### Running Tests
```powershell
composer test  # Clears config cache and runs PHPUnit
```

### Queue Management
Queue jobs use `database` driver with 3 retries, 10s backoff. Jobs dispatched for AI operations (summary generation, review moderation). Run worker with: `php artisan queue:listen --tries=1`

## Authentication & Authorization

- **Laravel Breeze** for auth scaffolding (routes in `routes/auth.php`)
- **Role-based access**: `User.role` = 'admin' | 'user'
- **AdminMiddleware** (`app/Http/Middleware/AdminMiddleware.php`): Registered as `'admin'` alias in `bootstrap/app.php`
- **Test Accounts** (seeded by AdminUserSeeder):
  - Admin: `admin@bookshare.com` / `admin123`
  - User: `user@bookshare.com` / `user123`

## Project-Specific Conventions

### Model Patterns
- Use **explicit relationships**: `owner()`, `borrower()`, `book()`, `genre()` on all models
- Cast datetime fields: `requested_at`, `approved_at`, `returned_at` in Loan model
- Boolean flags: `is_available` (Book), `is_ai_moderated` (Review) - always cast to boolean
- Mass assignment: Define `$fillable` explicitly, never use `$guarded`

### Service Layer Rules
- Services inject repositories via constructor (type-hinted interfaces, not implementations)
- Always log important actions: `Log::info("Book created: {$book->title} by user {$book->owner_id}")`
- Throw exceptions for authorization failures: `throw new \Exception('You are not authorized to update this book')`
- Return fresh models after updates: `return $book->fresh()`

### Repository Pattern
When adding a new repository:
1. Create interface in `app/Repositories/Contracts/` (e.g., `BookRepositoryInterface`)
2. Implement in `app/Repositories/Eloquent/` (e.g., `BookRepository`)
3. Bind in `RepositoryServiceProvider`: `$this->app->bind(Interface::class, Implementation::class)`
4. Always eager load relationships with `with()` to avoid N+1 queries

### Controller Conventions
- Frontend controllers in `app/Http/Controllers/Frontend/` - return views
- Admin controllers in `app/Http/Controllers/Admin/` - must use `'admin'` middleware
- Use Form Requests for validation (`app/Http/Requests/`)
- Inject services in constructor, not repositories directly

### Route Organization
- Frontend routes: `Route::middleware(['auth', 'verified'])->group(...)` in `routes/web.php`
- Admin routes: `Route::middleware(['auth', 'admin'])->prefix('admin')->group(...)`
- Named routes follow pattern: `books.index`, `loans.request`, `admin.books.index`

## AI Integration (OpenAI)

**Current State**: OpenAI PHP SDK installed (`openai-php/laravel`) but **not configured** (no API key in config). AI services have placeholder implementations:

- `SummaryService::generateSummary()`: Returns hardcoded text, caches for 7 days
- `RecommendationService`: Uses fallback logic (genre/wishlist matching)
- `ModerationService`: Always returns safe content

**To Enable**: Add `OPENAI_API_KEY` to `.env`, uncomment OpenAI client calls in AI services. All services cache results (7-day TTL for summaries, 30 days for recommendations).

## Database Patterns

- **Migrations**: Timestamped format `2025_01_01_000000_create_*_table.php`
- **Foreign keys**: Always use `foreignId('user_id')->constrained()` pattern
- **Indexes**: Add on frequently queried columns (e.g., `is_available`, `status`, `genre_id`)
- **Seeders**: Run order matters - AdminUserSeeder → GenreSeeder → BookSeeder

## Frontend (Blade + Tailwind + Alpine)

- Views in `resources/views/{frontend,auth,admin}/` - organized by feature
- Layouts in `resources/views/layouts/`
- Tailwind 3.x + Alpine.js for interactivity
- Build with `npm run build` (production) or `npm run dev` (watch mode)
- Vite config in `vite.config.js` - inputs: `resources/css/app.css`, `resources/js/app.js`

## Common Tasks

### Adding a New Feature
1. Create migration: `php artisan make:migration create_feature_table`
2. Create model in `app/Models/` with relationships and casts
3. Create repository interface + implementation, bind in RepositoryServiceProvider
4. Create service in `app/Services/` with business logic
5. Create controller (Frontend or Admin) + Form Requests
6. Add routes to `routes/web.php` with proper middleware
7. Create Blade views in `resources/views/`

### Debugging
- Logs: `storage/logs/laravel.log` or use `php artisan pail --timeout=0`
- Queue jobs: Check `jobs` table for pending, `failed_jobs` for errors
- DB queries: Enable query log or use Laravel Debugbar (not installed)

## Dependencies

- **PHP 8.2+**, Laravel 12, Laravel Breeze 2.3
- **OpenAI PHP SDK** 0.18 (optional, for AI features)
- **Database**: MySQL via XAMPP (local), configured in `.env`
- **Queue**: Database driver (uses `jobs` and `failed_jobs` tables)
- **Frontend**: Tailwind 3.x, Alpine.js 3.x, Vite 7.x

## Important Files

- `bootstrap/app.php`: Middleware aliases, service provider registration
- `config/services.php`: Third-party service credentials
- `setup.ps1`: Automated setup script for Windows/XAMPP
- `CONFIGURATION.md`: Detailed setup instructions with code examples
- `composer.json`: Custom scripts (`dev`, `setup`, `test`)
Below is your complete instruction file with Laravel 12 best practices and clean architecture:

text
# BookShare - Laravel 12 Project Instructions

**Course:** Applications Web Avancées | **Team Size:** 4-5 members | **Academic Year:** 2025-2026

---

## Project Overview

BookShare is a collaborative platform connecting reading enthusiasts to share, lend, and discover books while building a community and promoting sustainable reading habits.

### Tech Stack
- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade templates (Front Office + Back Office)
- **Database:** MySQL/PostgreSQL with Eloquent ORM
- **AI Integration:** OpenAI API via `openai-php/laravel` package
- **Version Control:** GitLab (mandatory)
- **Queue System:** Redis for background jobs
- **Caching:** Redis for AI response caching

---

## Architecture & Best Practices

### Folder Structure

app/
├── Http/
│ ├── Controllers/
│ │ ├── API/ # API endpoints
│ │ ├── Frontend/ # Front office controllers
│ │ └── Admin/ # Back office controllers
│ ├── Requests/ # Form validation
│ └── Middleware/
├── Models/ # Eloquent models
├── Services/ # Business logic layer
│ ├── BookService.php
│ ├── LoanService.php
│ ├── AI/
│ │ ├── RecommendationService.php
│ │ └── SummaryService.php
├── Repositories/ # Data access layer
│ ├── BookRepository.php
│ └── UserRepository.php
├── Jobs/ # Queue jobs for AI tasks
│ ├── GenerateBookSummary.php
│ └── ProcessRecommendations.php
└── DTOs/ # Data Transfer Objects

resources/
├── views/
│ ├── frontend/ # Front office Blade templates
│ └── admin/ # Back office Blade templates

database/
├── migrations/
├── seeders/
└── factories/

text

### Laravel 12 Architecture Patterns

#### 1. Repository Pattern (Data Access)
Repositories handle all database queries and abstract Eloquent logic from business logic.

**Example: BookRepository.php**
<?php namespace App\Repositories; use App\Models\Book; use Illuminate\Database\Eloquent\Collection; class BookRepository { public function findAvailableBooks(): Collection { return Book::where('is_available', true) ->with(['owner', 'genre']) ->latest() ->get(); } public function findByGenre(string $genre): Collection { return Book::whereHas('genre', function ($query) use ($genre) { $query->where('name', $genre); })->get(); } public function findById(int $id): ?Book { return Book::with(['owner', 'reviews', 'loans'])->find($id); } public function create(array $data): Book { return Book::create($data); } public function update(Book $book, array $data): bool { return $book->update($data); } } ``` #### 2. Service Layer (Business Logic) Services contain business rules and orchestrate repositories, AI services, and jobs. **Example: LoanService.php** ``` <?php namespace App\Services; use App\Models\Loan; use App\Models\Book; use App\Repositories\BookRepository; use App\Repositories\LoanRepository; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Notification; use App\Notifications\LoanRequestNotification; class LoanService { public function __construct( private BookRepository $bookRepository, private LoanRepository $loanRepository ) {} public function requestLoan(int $bookId, int $userId): Loan { return DB::transaction(function () use ($bookId, $userId) { $book = $this->bookRepository->findById($bookId); // Business validation if (!$book->is_available) { throw new \Exception('Book is not available for loan'); } if ($book->owner_id === $userId) { throw new \Exception('You cannot borrow your own book'); } // Create loan $loan = $this->loanRepository->create([ 'book_id' => $bookId, 'borrower_id' => $userId, 'status' => 'pending', 'requested_at' => now(), ]); // Update book availability $this->bookRepository->update($book, ['is_available' => false]); // Notify owner Notification::send($book->owner, new LoanRequestNotification($loan)); return $loan; }); } } ``` #### 3. AI Integration with Service Classes **Example: RecommendationService.php** ``` <?php namespace App\Services\AI; use OpenAI\Laravel\Facades\OpenAI; use App\Models\User; use App\Repositories\BookRepository; use Illuminate\Support\Facades\Cache; class RecommendationService { public function __construct( private BookRepository $bookRepository ) {} public function getRecommendations(User $user): array { // Cache AI recommendations for 1 hour return Cache::remember( "user_recommendations_{$user->id}", 3600, fn() => $this->generateRecommendations($user) ); } private function generateRecommendations(User $user): array { // Get user's reading history $history = $user->loans() ->with('book') ->completed() ->get() ->pluck('book.title') ->toArray(); $historyText = implode(', ', $history); // Call OpenAI API $result = OpenAI::chat()->create([ 'model' => 'gpt-4', 'messages' => [ [ 'role' => 'system', 'content' => 'You are a book recommendation expert.' ], [ 'role' => 'user', 'content' => "Based on these books: {$historyText}, recommend 5 similar books with titles and authors." ] ], 'max_tokens' => 500, ]); return $this->parseRecommendations($result['choices']['message']['content']); } private function parseRecommendations(string $content): array { // Parse AI response and return structured data // Implementation details... } } ``` #### 4. Background Jobs for AI Tasks **Example: GenerateBookSummary.php** ``` <?php namespace App\Jobs; use App\Models\Book; use OpenAI\Laravel\Facades\OpenAI; use Illuminate\Bus\Queueable; use Illuminate\Contracts\Queue\ShouldQueue; use Illuminate\Foundation\Bus\Dispatchable; use Illuminate\Queue\InteractsWithQueue; use Illuminate\Queue\SerializesModels; use Illuminate\Support\Facades\Log; class GenerateBookSummary implements ShouldQueue { use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; public int $tries = 3; public int $backoff = 10; public function __construct( private Book $book ) {} public function handle(): void { try { $result = OpenAI::chat()->create([ 'model' => 'gpt-3.5-turbo', 'messages' => [ [ 'role' => 'user', 'content' => "Generate a 3-sentence summary for the book '{$this->book->title}' by {$this->book->author}" ] ], 'max_tokens' => 150, ]); $summary = $result['choices']['message']['content']; $this->book->update(['ai_summary' => $summary]); Log::info("Summary generated for book {$this->book->id}"); } catch (\Exception $e) { Log::error("Failed to generate summary: " . $e->getMessage()); throw $e; } } } ``` --- ## Core Features & Requirements ### Shared Team Tasks (Everyone) 1. **Authentication Module** - Laravel Breeze/Fortify for auth scaffolding - User registration, login, password reset - Role-based access (User, Admin) 2. **Template Integration** - Front Office: Public-facing book catalog, search, user profiles - Back Office: Admin dashboard for managing users, books, loans ### Individual CRUD Modules (Assign to team members) #### Module 1: Book Management - CRUD operations for books (create, read, update, delete) - Eloquent relationships: `Book belongsTo User (owner)`, `Book hasMany Loan`, `Book hasMany Review` - Form validation using Laravel Request classes - **AI Feature:** Auto-generate book summary on creation (background job) #### Module 2: Loan/Request System - CRUD for loan requests with status workflow (pending → approved → returned) - Eloquent relationships: `Loan belongsTo Book`, `Loan belongsTo User (borrower)` - Notifications when loans are requested/approved - **AI Feature:** Smart loan duration recommendations based on book type #### Module 3: Review & Rating System - CRUD for book reviews and ratings (1-5 stars) - Eloquent relationships: `Review belongsTo Book`, `Review belongsTo User` - Average rating calculation - **AI Feature:** Content moderation (flag inappropriate reviews using AI) #### Module 4: Wishlist & Recommendations - CRUD for user wishlists - Display personalized recommendations on user dashboard - **AI Feature:** AI-powered book recommendations based on reading history #### Module 5: Search & Discovery - Advanced search with filters (genre, author, availability) - Laravel Scout integration for full-text search - **AI Feature:** Semantic search using OpenAI embeddings --- ## AI Implementation Best Practices ### 1. Environment Configuration ``` # .env OPENAI_API_KEY=sk-your-api-key QUEUE_CONNECTION=redis CACHE_DRIVER=redis ``` ### 2. Install OpenAI Package ``` composer require openai-php/laravel php artisan vendor:publish --provider="OpenAI\Laravel\ServiceProvider" ``` ### 3. Use Queues for All AI Calls Never call AI APIs synchronously—always use queued jobs to avoid timeouts. ``` // In controller use App\Jobs\GenerateBookSummary; GenerateBookSummary::dispatch($book)->onQueue('ai-tasks'); ``` ### 4. Cache AI Responses AI API calls are expensive—cache results aggressively. ``` Cache::remember('book_summary_' . $book->id, 86400, function() use ($book) { return $this->generateSummary($book); }); ``` ### 5. Rate Limiting Protect AI endpoints with Laravel's rate limiter. ``` // routes/web.php Route::middleware('throttle:10,1')->group(function () { Route::post('/ai/recommendations', [AIController::class, 'recommend']); }); ``` ### 6. Error Handling & Logging Always log AI requests and handle failures gracefully. ``` try { $result = OpenAI::chat()->create([...]); } catch (\Exception $e) { Log::error('OpenAI API failed: ' . $e->getMessage()); return response()->json(['error' => 'AI service unavailable'], 503); } ``` ### 7. Testing AI Services Mock OpenAI in tests—never call real API. ``` use OpenAI\Laravel\Facades\OpenAI; OpenAI::fake([ 'choices' => [ ['message' => ['content' => 'Mock recommendation']], ], ]); ``` --- ## Database Schema ### Key Tables & Relationships ``` // users table Schema::create('users', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('email')->unique(); $table->string('password'); $table->enum('role', ['user', 'admin'])->default('user'); $table->timestamps(); }); // books table Schema::create('books', function (Blueprint $table) { $table->id(); $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); $table->string('title'); $table->string('author'); $table->string('isbn')->nullable(); $table->foreignId('genre_id')->constrained(); $table->text('description')->nullable(); $table->text('ai_summary')->nullable(); $table->boolean('is_available')->default(true); $table->timestamps(); }); // loans table Schema::create('loans', function (Blueprint $table) { $table->id(); $table->foreignId('book_id')->constrained()->onDelete('cascade'); $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade'); $table->enum('status', ['pending', 'approved', 'returned', 'rejected'])->default('pending'); $table->timestamp('requested_at'); $table->timestamp('approved_at')->nullable(); $table->timestamp('returned_at')->nullable(); $table->timestamps(); }); // reviews table Schema::create('reviews', function (Blueprint $table) { $table->id(); $table->foreignId('book_id')->constrained()->onDelete('cascade'); $table->foreignId('user_id')->constrained()->onDelete('cascade'); $table->integer('rating'); // 1-5 $table->text('comment')->nullable(); $table->boolean('is_flagged')->default(false); $table->timestamps(); }); ``` --- ## Form Validation Best Practices Use Form Request classes for validation logic. **Example: StoreBookRequest.php** ``` <?php namespace App\Http\Requests; use Illuminate\Foundation\Http\FormRequest; class StoreBookRequest extends FormRequest { public function authorize(): bool { return auth()->check(); } public function rules(): array { return [ 'title' => 'required|string|max:255', 'author' => 'required|string|max:255', 'isbn' => 'nullable|string|unique:books,isbn', 'genre_id' => 'required|exists:genres,id', 'description' => 'nullable|string|max:2000', 'cover_image' => 'nullable|image|max:2048', ]; } public function messages(): array { return [ 'title.required' => 'Le titre du livre est obligatoire.', 'isbn.unique' => 'Ce ISBN existe déjà dans la base de données.', ]; } } ``` --- ## GitLab Workflow ### Branch Strategy - `main` → production-ready code - `develop` → integration branch - `feature/book-management` → individual features - `hotfix/fix-loan-bug` → urgent fixes ### Commit Message Format ``` feat: add AI recommendation service fix: resolve loan status update bug refactor: optimize book query with eager loading docs: update API documentation ``` ### Merge Request Process 1. Create feature branch from `develop` 2. Commit your changes with clear messages 3. Push to GitLab and create Merge Request 4. Assign 2 team members for code review 5. Fix review comments 6. Merge to `develop` after approval --- ## Deployment Checklist ### Production Requirements - Set `APP_ENV=production` in `.env` - Enable caching: `php artisan config:cache`, `php artisan route:cache` - Run migrations: `php artisan migrate --force` - Setup queue worker with Supervisor - Configure Redis for queues and caching - Enable HTTPS - Set proper file permissions (storage, bootstrap/cache) --- ## Evaluation Criteria **Individual (70%)** - Code quality and Laravel best practices - Proper use of Eloquent relationships - Form validation implementation - AI feature integration - Git commit history and MR participation **Team (30%)** - Authentication module completeness - Template integration (front + back office) - Overall app functionality - Code consistency across modules - Documentation and README **Critical Rules:** - Individual mark < 10 → team mark ignored - No GitLab project → complaints not considered - If |individual - team| > 3 → weights adjust to 60/40 --- ## Resources - [Laravel 12 Documentation](https://laravel.com/docs/12.x) - [OpenAI Laravel Package](https://github.com/openai-php/laravel) - [Laravel Scout Documentation](https://laravel.com/docs/12.x/scout) - [Repository Pattern in Laravel](https://muneebdev.com/service-layer-laravel-tutorial/) - [Laravel Queue Documentation](https://laravel.com/docs/12.x/queues) --- ## Quick Start Commands ``` # Install Laravel 12 composer create-project laravel/laravel bookshare "^12.0" # Install dependencies composer require openai-php/laravel composer require laravel/breeze --dev # Setup authentication php artisan breeze:install blade npm install && npm run build # Database setup php artisan migrate php artisan db:seed # Run queue worker php artisan queue:work --queue=ai-tasks # Start development server php artisan serve ``` --- **Good luck with your project! Remember: keep business logic in services, data access in repositories, and always queue AI tasks.** 🚀 ``` *** This instruction file gives your team everything needed: clear architecture, Laravel 12 best practices, AI integration patterns, and specific code examples for BookShare. Store this as `INSTRUCTIONS.md` in your GitLab repo root and share with your team.[5][4][1][3]