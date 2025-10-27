<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDietaRequest extends FormRequest
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
            'name' => ['required'],
            'time' => ['required'],
            'day' => ['required'],
            'observation' => ['nullable'],

            'alimentos' => ['required', 'array'],
            'alimentos.*.name' => ['required', 'string', 'max:255'],
            'alimentos.*.quantidade' => ['required', 'numeric', 'min:1'],

            'suplementos' => ['nullable', 'array'],        
            'suplementos.*.name' => ['nullable', 'string', 'max:255'],
            'suplementos.*.quantidade' => ['nullable', 'numeric', 'min:1'],
        ];
    }
}
