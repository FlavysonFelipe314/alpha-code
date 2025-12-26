<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnotacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ["required","max:255"],
            'content' => ["nullable"],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif', 'max:102400'], // 100MB max
        ];
    }
}
