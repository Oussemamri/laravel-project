# 📚 BookShare

**Share books, build community, read sustainably.**

BookShare is a Laravel-powered platform that connects book lovers to share, borrow, and discover books. Whether you have a shelf full of reads you've finished or you're looking for your next adventure, BookShare makes it easy to connect with others who love reading as much as you do.

## ✨ What Makes BookShare Special?

- **Easy Book Sharing**: List books you're willing to lend with just a few clicks
- **Smart Loan Management**: Request, approve, and track book loans seamlessly
- **AI-Powered Summaries**: Get quick AI-generated summaries to help you decide what to read next
- **Community Reviews**: Share your thoughts and see what others think about books
- **Personalized Recommendations**: Discover books based on your reading history
- **Wishlist System**: Keep track of books you want to read

## 🚀 Getting Started

### What You'll Need

Before diving in, make sure you have:
- **PHP 8.2 or higher**
- **Composer** (for PHP dependencies)
- **Node.js & npm** (for frontend assets)
- **MySQL** (we recommend XAMPP for local development on Windows)
- **Git** (for version control)

### Quick Setup (Windows with XAMPP)

We've made setup super easy with an automated script:

1. **Clone the project**:
   ```powershell
   git clone <your-repo-url>
   cd bookshare-app
   ```

2. **Run the setup script**:
   ```powershell
   .\setup.ps1
   ```
   
   This script will:
   - Install all dependencies (PHP & JavaScript)
   - Create your database
   - Set up your `.env` file
   - Run migrations and seed sample data
   - Build frontend assets

3. **Start the application**:
   ```powershell
   php artisan serve
   ```
   
   For AI features (optional), open a second terminal and run:
   ```powershell
   php artisan queue:work --queue=ai-tasks
   ```

4. **Open your browser** and visit `http://localhost:8000`

### First Login

The setup creates test accounts for you:

- **Admin Account**: admin@bookshare.com / admin123
- **Regular User**: user@bookshare.com / user123

## 🎨 Key Features Explained

### For Book Owners
- Add your books with title, author, genre, and description
- Mark books as available or unavailable for lending
- Receive notifications when someone wants to borrow
- Track who has your books and when they're due back

### For Book Borrowers
- Browse available books in the community
- Request to borrow books with a simple click
- Leave reviews and ratings after reading
- Build your wishlist of books you want to read

### AI Magic 🤖
- **Auto-Generated Summaries**: When you add a book, AI creates a quick 3-sentence summary
- **Smart Recommendations**: Based on what you've read, get personalized book suggestions
- **Review Moderation**: AI helps flag inappropriate content to keep the community friendly

> **Note**: AI features require an OpenAI API key. Add yours to `.env` as `OPENAI_API_KEY=your-key-here`

## 🛠️ Technical Stack

Built with modern, reliable technologies:
- **Backend**: Laravel 12 with PHP 8.2
- **Frontend**: Blade templates with Tailwind CSS & Alpine.js
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel Breeze
- **AI Integration**: OpenAI API via `openai-php/laravel`
- **Queue System**: Database-driven background jobs
- **Asset Build**: Vite for lightning-fast builds

## 📖 Common Tasks

### Running Tests
```powershell
composer test
```

### Starting Queue Worker (for AI features)
```powershell
php artisan queue:work --queue=ai-tasks
```

### Building Assets for Production
```powershell
npm run build
```

### Clearing Cache
```powershell
php artisan config:clear
php artisan cache:clear
```

## 🏗️ Project Structure

The codebase follows clean architecture principles:

```
app/
├── Http/Controllers/     # Frontend & Admin controllers
├── Services/             # Business logic (BookService, LoanService, etc.)
├── Repositories/         # Data access layer
├── Jobs/                 # Background jobs for AI tasks
└── Models/               # Eloquent models

resources/
├── views/
│   ├── frontend/        # Public-facing pages
│   └── admin/           # Admin dashboard
```

## 🤝 Contributing

This is an academic project, but we welcome improvements! If you'd like to contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 🐛 Troubleshooting

**Database connection errors?**
- Make sure XAMPP MySQL is running
- Check your `.env` database credentials

**Assets not loading?**
- Run `npm install` and `npm run build`
- Clear browser cache

**AI summaries not working?**
- Verify your OpenAI API key in `.env`
- Check if queue worker is running
- View logs in `storage/logs/laravel.log`

**Rate limit errors from OpenAI?**
- Free tier has strict limits (~3 requests/minute)
- Wait a few minutes between requests
- Consider adding credits to your OpenAI account

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 💬 Questions or Issues?

Check out our detailed setup guide in `CONFIGURATION.md` or open an issue if you run into problems.

Happy reading! 📖✨
