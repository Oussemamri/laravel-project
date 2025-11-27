<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Book Catalog') }}
            </h2>
            <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Book
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filter/Search Bar -->
            <div class="mb-4 bg-white rounded-lg shadow-sm p-3">
                <form method="GET" action="{{ route('books.index') }}" class="flex gap-2 items-center text-sm">
                    <select name="genre_id" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" onchange="this.form.submit()">
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->id }}" {{ request('genre_id') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->name }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="search" placeholder="Search books..." value="{{ request('search') }}" class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded-md hover:bg-gray-700">Search</button>
                    @if(request('search') || request('genre_id'))
                        <a href="{{ route('books.index') }}" class="px-3 py-1.5 bg-gray-300 text-gray-700 text-sm rounded-md hover:bg-gray-400">Clear</a>
                    @endif
                </form>
            </div>

            @if($books->isEmpty())
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No books available</h3>
                    <p class="mt-2 text-sm text-gray-500">Get started by adding your first book to share.</p>
                    <div class="mt-6">
                        <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                            Add Your First Book
                        </a>
                    </div>
                </div>
            @else
                <div x-data="{ view: 'grid' }">
                    <!-- View Toggle -->
                    <div class="flex justify-end mb-4">
                        <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1">
                            <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 text-sm font-medium rounded-md transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                            <button @click="view = 'list'" :class="view === 'list' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-gray-900'" class="px-4 py-2 text-sm font-medium rounded-md transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Grid View -->
                    <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-4">
                        @foreach($books as $book)
                            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group cursor-pointer">
                                <!-- Book Cover -->
                                <div class="aspect-[3/4] bg-gradient-to-br from-indigo-50 to-purple-50 relative overflow-hidden">
                                    @if($book->cover_image)
                                        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.src='https://via.placeholder.com/300x400/6366f1/ffffff?text={{ urlencode(substr($book->title, 0, 20)) }}'">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-24 h-24 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold shadow-lg {{ $book->is_available ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                            {{ $book->is_available ? 'Available' : 'Borrowed' }}
                                        </span>
                                    </div>
                                    <!-- Genre Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/90 text-indigo-700 shadow-lg">
                                            {{ $book->genre->name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Book Info -->
                                <div class="p-4">
                                    <h3 class="font-bold text-base mb-1 line-clamp-2 group-hover:text-indigo-600 transition leading-snug" title="{{ $book->title }}">
                                        {{ $book->title }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2 truncate font-medium" title="{{ $book->author }}">
                                        by {{ $book->author }}
                                    </p>
                                    <p class="text-xs text-gray-500 mb-3 truncate">
                                        Shared by {{ $book->owner->name }}
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="space-y-2">
                                        <a href="{{ route('books.show', $book) }}" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow-md">
                                            View Details
                                        </a>
                                        @if($book->owner_id == auth()->id())
                                            <!-- Owner Actions -->
                                            <a href="{{ route('books.edit', $book) }}" class="block text-center bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow-md">
                                                Edit Book
                                            </a>
                                        @elseif($book->is_available)
                                            <!-- Borrow Action -->
                                            <form action="{{ route('loans.request', $book) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 px-4 rounded-lg transition shadow-sm hover:shadow-md">
                                                    Borrow This Book
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- List View -->
                    <div x-show="view === 'list'" x-cloak class="space-y-4 mt-4">
                        @foreach($books as $book)
                            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                                <div class="flex flex-col sm:flex-row">
                                    <!-- Book Cover -->
                                    <div class="sm:w-48 flex-shrink-0">
                                        <div class="aspect-[3/4] sm:h-full bg-gradient-to-br from-indigo-50 to-purple-50 relative overflow-hidden">
                                            @if($book->cover_image)
                                                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/300x400/6366f1/ffffff?text={{ urlencode(substr($book->title, 0, 20)) }}'">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-20 h-20 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Book Details -->
                                    <div class="flex-1 p-6">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                                        {{ $book->genre->name }}
                                                    </span>
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $book->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $book->is_available ? 'Available' : 'Borrowed' }}
                                                    </span>
                                                </div>
                                                <h3 class="font-bold text-xl mb-2 text-gray-900 hover:text-indigo-600 transition">
                                                    {{ $book->title }}
                                                </h3>
                                                <p class="text-base text-gray-600 mb-2 font-medium">
                                                    by {{ $book->author }}
                                                </p>
                                                <p class="text-sm text-gray-500 mb-3">
                                                    Shared by <span class="font-medium text-gray-700">{{ $book->owner->name }}</span>
                                                </p>
                                                @if($book->description)
                                                    <p class="text-sm text-gray-600 line-clamp-2">
                                                        {{ $book->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex gap-3 mt-4">
                                            <a href="{{ route('books.show', $book) }}" class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow-md">
                                                View Details
                                            </a>
                                            @if($book->owner_id == auth()->id())
                                                <!-- Owner Actions -->
                                                <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow-md">
                                                    Edit Book
                                                </a>
                                            @elseif($book->is_available)
                                                <!-- Borrow Action -->
                                                <form action="{{ route('loans.request', $book) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 px-6 rounded-lg transition shadow-sm hover:shadow-md">
                                                        Borrow This Book
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
