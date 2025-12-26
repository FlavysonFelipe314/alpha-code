<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgendaRequest extends FormRequest
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
            'title' => ['sometimes','required','string','max:255'],
            'date' => ['sometimes','required','date'],
            'time' => ['sometimes','required','date_format:H:i'],
            'duration' => ['nullable','integer','min:1'],
            'category' => ['nullable','string','max:100'],
            'notes' => ['nullable','string'],
            'completed' => ['nullable','boolean'],
        ];
    }
}
