<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'priority' => ['nullable', 'integer', 'min:1'],
            'filter_project' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a task name.',
            'name.max' => 'The task name may not exceed 255 characters.',
            'project_id.exists' => 'The selected project does not exist.',
            'priority.min' => 'Priority must be at least 1.',
        ];
    }
}

