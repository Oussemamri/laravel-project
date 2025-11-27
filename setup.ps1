# BookShare Laravel Setup Script
Write-Host "=== BookShare Setup Script ===" -ForegroundColor Cyan

# Check if MySQL is running
Write-Host "`nChecking MySQL status..." -ForegroundColor Yellow
$mysql = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysql) {
    Write-Host "ERROR: MySQL is not running!" -ForegroundColor Red
    Write-Host "Please start XAMPP Control Panel and start MySQL service" -ForegroundColor Yellow
    Write-Host "Then run this script again." -ForegroundColor Yellow
    exit 1
}
Write-Host "MySQL is running!" -ForegroundColor Green

# Create database
Write-Host "`nCreating database 'bookshare'..." -ForegroundColor Yellow
$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$createDbQuery = "CREATE DATABASE IF NOT EXISTS bookshare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
& $mysqlPath -u root -e $createDbQuery 2>$null
if ($?) {
    Write-Host "Database created successfully!" -ForegroundColor Green
} else {
    Write-Host "Error creating database. It may already exist." -ForegroundColor Yellow
}

# Run migrations
Write-Host "`nRunning database migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($?) {
    Write-Host "Migrations completed!" -ForegroundColor Green
} else {
    Write-Host "Error running migrations!" -ForegroundColor Red
    exit 1
}

# Create seeders if they don't exist
Write-Host "`nCreating seeders..." -ForegroundColor Yellow
php artisan make:seeder AdminUserSeeder --quiet
php artisan make:seeder GenreSeeder --quiet

# Run seeders
Write-Host "`nSeeding database..." -ForegroundColor Yellow
php artisan db:seed --class=AdminUserSeeder --force
php artisan db:seed --class=GenreSeeder --force
Write-Host "Database seeded!" -ForegroundColor Green

# Install npm dependencies
Write-Host "`nInstalling npm dependencies..." -ForegroundColor Yellow
npm install
Write-Host "npm dependencies installed!" -ForegroundColor Green

# Build frontend assets
Write-Host "`nBuilding frontend assets..." -ForegroundColor Yellow
npm run build
Write-Host "Frontend assets built!" -ForegroundColor Green

Write-Host "`n=== Setup Complete! ===" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "1. Update .env file with your OpenAI API key (OPENAI_API_KEY)"
Write-Host "2. Register routes in routes/web.php"
Write-Host "3. Register RepositoryServiceProvider in bootstrap/app.php"
Write-Host "4. Register AdminMiddleware in bootstrap/app.php"
Write-Host "5. Run: php artisan serve"
Write-Host "`nDefault admin credentials:" -ForegroundColor Yellow
Write-Host "Email: admin@bookshare.com"
Write-Host "Password: admin123"
