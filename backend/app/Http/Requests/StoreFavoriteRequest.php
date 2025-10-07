<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pokemon_id' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'image' => 'required|string|url',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'pokemon_id.required' => 'El ID del Pokémon es obligatorio.',
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'image.required' => 'La imagen es obligatoria.',
            'image.url' => 'La imagen debe ser una URL válida.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
        ];
    }
}