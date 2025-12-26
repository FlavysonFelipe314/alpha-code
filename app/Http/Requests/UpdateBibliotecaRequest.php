<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBibliotecaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:255'],
            'author' => ['nullable','string','max:255'],
            'type' => ['nullable','string','max:50'],
            'status' => ['nullable','in:in-progress,completed,wishlist'],
            'progress' => ['nullable','integer','between:0,100'],
            'notes' => ['nullable','string'],
            'file' => ['nullable','file','mimes:pdf,mp4,avi,mkv,mov,wmv,webm','max:102400'], // 100MB max
        ];
    }
}
