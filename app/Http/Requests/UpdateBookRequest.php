<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn,' . $bookId,
            'genre_id' => 'sometimes|exists:genres,id',
            'description' => 'nullable|string|max:2000',
            'is_available' => 'sometimes|boolean',
            'cover_image' => 'nullable|url|max:500',
            'cover_image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.string' => 'Le titre doit être une chaîne de caractères.',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'author.string' => 'L\'auteur doit être une chaîne de caractères.',
            'author.max' => 'L\'auteur ne peut pas dépasser 255 caractères.',
            'isbn.unique' => 'Ce ISBN existe déjà dans la base de données.',
            'genre_id.exists' => 'Le genre sélectionné n\'existe pas.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'is_available.boolean' => 'La disponibilité doit être vraie ou fausse.',
        ];
    }
}
