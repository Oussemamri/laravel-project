<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Cover Image Upload -->
                            <div class="md:col-span-1">
                                <label class="block font-medium text-sm text-gray-700 mb-2">Book Cover</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-indigo-400 transition" id="image-preview-container">
                                    <div id="image-preview" class="hidden">
                                        <img src="" alt="Preview" class="mx-auto max-h-64 rounded">
                                        <button type="button" onclick="clearImage()" class="mt-2 text-sm text-red-600 hover:text-red-800">Remove</button>
                                    </div>
                                    <div id="image-placeholder">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <label for="cover_image_file" class="mt-2 block text-sm text-gray-600 cursor-pointer">
                                            <span class="text-indigo-600 hover:text-indigo-500 font-medium">Upload a file</span>
                                            <span class="text-gray-500"> or enter URL below</span>
                                        </label>
                                        <input type="file" id="cover_image_file" name="cover_image_file" accept="image/*" class="hidden" onchange="previewImage(event)">
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <input type="url" name="cover_image" id="cover_image" placeholder="Or paste image URL" value="{{ old('cover_image') }}"
                                           class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                @error('cover_image')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Book Details -->
                            <div class="md:col-span-2 space-y-4">
                                <div>
                                    <label for="title" class="block font-medium text-sm text-gray-700 mb-1">Title *</label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                           placeholder="Enter book title"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('title')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="author" class="block font-medium text-sm text-gray-700 mb-1">Author *</label>
                                    <input type="text" name="author" id="author" value="{{ old('author') }}" required
                                           placeholder="Enter author name"
                                           class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('author')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="isbn" class="block font-medium text-sm text-gray-700 mb-1">ISBN</label>
                                        <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}"
                                               placeholder="978-0-123456-78-9"
                                               class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('isbn')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="genre_id" class="block font-medium text-sm text-gray-700 mb-1">Genre *</label>
                                        <select name="genre_id" id="genre_id" required
                                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Select genre</option>
                                            @foreach($genres as $genre)
                                                <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                                                    {{ $genre->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('genre_id')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="description" class="block font-medium text-sm text-gray-700 mb-1">Description</label>
                                    <textarea name="description" id="description" rows="6"
                                              placeholder="Tell us about this book..."
                                              class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">AI-Powered Features</p>
                                    <p class="mt-1">Once you add your book, our AI will automatically generate a summary and personalized recommendations for other users!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t">
                            <a href="{{ route('books.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Book
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').querySelector('img').src = e.target.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                    document.getElementById('image-placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function clearImage() {
            document.getElementById('cover_image_file').value = '';
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('image-placeholder').classList.remove('hidden');
        }
    </script>
</x-app-layout>
