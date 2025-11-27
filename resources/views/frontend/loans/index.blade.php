<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Loans') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Books I'm Borrowing -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Books I'm Borrowing
                            <span class="ml-auto text-sm font-normal text-gray-500">({{ $borrowedLoans->count() }} books)</span>
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @forelse($borrowedLoans as $loan)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex gap-4">
                                    <!-- Book Cover -->
                                    <div class="flex-shrink-0">
                                        <div class="w-20 h-28 bg-gradient-to-br from-indigo-50 to-purple-50 rounded overflow-hidden">
                                            @if($loan->book->cover_image)
                                                <img src="{{ $loan->book->cover_image }}" alt="{{ $loan->book->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Book Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-semibold text-gray-900 truncate">
                                            {{ $loan->book->title }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-1">by {{ $loan->book->author }}</p>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Owner: {{ $loan->book->owner->name }}
                                        </p>
                                        
                                        <!-- Status Badge -->
                                        <div class="flex items-center gap-2 mb-3">
                                            @if($loan->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    ⏳ Pending Approval
                                                </span>
                                            @elseif($loan->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    ✓ Approved
                                                </span>
                                            @elseif($loan->status === 'returned')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    ↩ Returned
                                                </span>
                                            @elseif($loan->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    ✗ Rejected
                                                </span>
                                            @endif
                                            
                                            <span class="text-xs text-gray-500">
                                                {{ $loan->requested_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex gap-2">
                                            <a href="{{ route('books.show', $loan->book) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition">
                                                View Book
                                            </a>
                                            
                                            @if($loan->status === 'approved')
                                                <form action="{{ route('loans.return', $loan) }}" method="POST" onsubmit="return confirm('Mark this book as returned?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition">
                                                        ↩ Return Book
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-sm">You haven't borrowed any books yet.</p>
                                <a href="{{ route('books.index') }}" class="inline-block mt-3 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                    Browse available books →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Loan Requests for My Books -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Loan Requests for My Books
                            <span class="ml-auto text-sm font-normal text-gray-500">({{ $ownerLoans->count() }} requests)</span>
                        </h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @forelse($ownerLoans as $loan)
                            <div class="p-4 hover:bg-gray-50 transition">
                                <div class="flex gap-4">
                                    <!-- Book Cover -->
                                    <div class="flex-shrink-0">
                                        <div class="w-20 h-28 bg-gradient-to-br from-amber-50 to-orange-50 rounded overflow-hidden">
                                            @if($loan->book->cover_image)
                                                <img src="{{ $loan->book->cover_image }}" alt="{{ $loan->book->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Request Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-base font-semibold text-gray-900 truncate">
                                            {{ $loan->book->title }}
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-1">by {{ $loan->book->author }}</p>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Requested by: <span class="font-medium text-gray-700">{{ $loan->borrower->name }}</span>
                                        </p>
                                        
                                        <!-- Status Badge -->
                                        <div class="flex items-center gap-2 mb-3">
                                            @if($loan->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    ⏳ Awaiting Your Response
                                                </span>
                                            @elseif($loan->status === 'approved')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    ✓ You Approved
                                                </span>
                                            @elseif($loan->status === 'returned')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    ↩ Returned
                                                </span>
                                            @elseif($loan->status === 'rejected')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                    ✗ You Rejected
                                                </span>
                                            @endif
                                            
                                            <span class="text-xs text-gray-500">
                                                {{ $loan->requested_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Actions for Owner -->
                                        <div class="flex gap-2">
                                            @if($loan->status === 'pending')
                                                <form action="{{ route('loans.accept', $loan) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition">
                                                        ✓ Approve
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('loans.reject', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Reject this loan request?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                                        ✗ Reject
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route('books.show', $loan->book) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold rounded-lg transition">
                                                View Book
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">No loan requests for your books yet.</p>
                                <a href="{{ route('books.create') }}" class="inline-block mt-3 text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                    Add more books to share →
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
