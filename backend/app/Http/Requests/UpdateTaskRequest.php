<?php

namespace App\Http\Requests;

use App\Enums\{TaskPriority, TaskStatus};
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                Rule::exists(Task::class, 'id')->where('user_id', auth()->id())->whereNull('deleted_at')
            ],
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],
            'description' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],
            'status' => [
                'sometimes',
                'required',
                Rule::in(TaskStatus::getValues())
            ],
            'priority' => [
                'sometimes',
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
