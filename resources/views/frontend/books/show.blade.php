<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                        <!-- Book Cover -->
                        <div class="md:col-span-1">
                            <div class="aspect-[2/3] bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg overflow-hidden shadow-lg">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/400x600/6366f1/ffffff?text={{ urlencode($book->title) }}'">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-32 h-32 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="mt-4 text-center">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $book->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="5"/>
                                    </svg>
                                    {{ $book->is_available ? 'Available' : 'Currently Borrowed' }}
                                </span>
                            </div>
                        </div>

                        <!-- Book Details -->
                        <div class="md:col-span-2">
                            <div class="mb-3">
                                <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $book->genre->name }}
                                </span>
                            </div>
                            
                            <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $book->title }}</h1>
                            <p class="text-2xl text-gray-600 mb-6">by {{ $book->author }}</p>
                            
                            <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Owner</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $book->owner->name }}</p>
                                </div>
                                @if($book->isbn)
                                <div>
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">ISBN</p>
                                    <p class="mt-1 text-lg font-mono text-gray-900">{{ $book->isbn }}</p>
                                </div>
                                @endif
                            </div>

                            @if($book->description)
                            <div class="mb-6">
                                <h3 class="font-bold text-xl text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Description
                                </h3>
                                <p class="text-gray-700 leading-relaxed">{{ $book->description }}</p>
                            </div>
                            @endif

                            @if($book->ai_summary)
                            <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-100">
                                <h3 class="font-bold text-xl text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    AI-Generated Summary
                                </h3>
                                <p class="text-gray-700 leading-relaxed">{{ $book->ai_summary }}</p>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 mt-8">
                                @if($book->owner_id == auth()->id())
                                    <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Book
                                    </a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    @if($book->is_available)
                                    <form action="{{ route('loans.request', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition shadow-lg">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Request to Borrow
                                        </button>
                                    </form>
                                    @endif
                                @endif
                                <a href="{{ route('books.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Back to Catalog
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="font-bold text-2xl mb-4">Reviews</h3>
                        
                        @if($book->reviews->isEmpty())
                            <p class="text-gray-500">No reviews yet.</p>
                        @else
                            <div class="space-y-4">
                                @foreach($book->reviews as $review)
                                    <div class="bg-gray-50 p-4 rounded">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-semibold">{{ $review->user->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="text-yellow-500">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        ★
                                                    @else
                                                        ☆
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-gray-700">{{ $review->comment }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
