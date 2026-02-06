<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatusEnum;
use App\Enums\TaskPriorityEnum;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Add specific authorization logic if needed, e.g., $this->user()->can('create', Task::class)
        // For now, we rely on the Controller's middleware or Policy called before this request mainly.
        // However, usually permission logic sits in Policies.
        // Let's return true and rely on Route Middleware/Policy for access control.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => ['required', Rule::enum(TaskPriorityEnum::class)],
            'status' => ['required', Rule::enum(TaskStatusEnum::class)],
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ];
    }
}
