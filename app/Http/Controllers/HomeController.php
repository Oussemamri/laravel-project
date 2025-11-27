<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display the landing page.
     */
    public function index(): View
    {
        // Get some featured/recent books to showcase
        $featuredBooks = $this->bookService->getAvailableBooks(6);
        
        return view('welcome', compact('featuredBooks'));
    }
}
