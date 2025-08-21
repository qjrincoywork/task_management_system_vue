<?php

namespace App\Http\Requests;

use App\Enums\{SortOrder, TaskPriority, TaskStatus};
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskListRequest extends FormRequest
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
            'search' => [
                'nullable',
                'string',
                'max:255'
            ],
            'priority' => [
                'nullable',
                'string',
                Rule::in(TaskPriority::getValues())
            ],
            'sort_order' => [
                'nullable',
                'string',
                Rule::in(SortOrder::getValues())
            ],
            'status' => [
                'nullable',
                'integer',
                Rule::in(TaskStatus::getValues())
            ],
            'per_page' => [
                'nullable',
                'integer'
            ],
        ];
    }
}
