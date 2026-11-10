<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120', 'unique:projects,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a project name.',
            'name.max' => 'The project name may not exceed 120 characters.',
            'name.unique' => 'A project with this name already exists.',
        ];
    }
}

