<?php

namespace App\Http\Requests;

use App\Enums\{TaskPriority, TaskStatus};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'required',
                'string',
                'max:255'
            ],
            'status' => [
                'required',
                Rule::in(TaskStatus::getValues())
            ],
            'priority' => [
                'required',
                Rule::in(TaskPriority::getValues())
            ],
            'order' => [
                'nullable',
                'integer',
                'min:0'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.max' => 'Task description cannot exceed 1000 characters.',
            'status.in' => 'Invalid task status.',
            'priority.in' => 'Invalid task priority.',
            'order.integer' => 'Order must be a number.',
            'order.min' => 'Order cannot be negative.',
        ];
    }
}
