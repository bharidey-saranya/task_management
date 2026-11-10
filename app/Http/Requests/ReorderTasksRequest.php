<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_ids' => ['required', 'array', 'min:1'],
            'task_ids.*' => ['required', 'integer', 'distinct', 'exists:tasks,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'task_ids.required' => 'No tasks were specified for reordering.',
            'task_ids.*.exists' => 'One or more tasks could not be found.',
        ];
    }
}

