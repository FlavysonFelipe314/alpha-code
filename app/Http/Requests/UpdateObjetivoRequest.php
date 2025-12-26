<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateObjetivoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes','required','string','max:255'],
            'topic' => ['nullable','string','max:100'],
            'description' => ['nullable','string'],
            'deadline' => ['nullable','date'],
            'completed' => ['nullable','boolean'],
            'reminders' => ['nullable','array'],
            'reminders.*.text' => ['required_with:reminders','string','max:255'],
            'reminders.*.completed' => ['nullable','boolean'],
        ];
    }
}
