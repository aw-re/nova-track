<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequestRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'task_id' => 'nullable|exists:tasks,id',
            'resource_id' => 'nullable|exists:resources,id',
            'resource_type' => 'required|string|max:100',
            'resource_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01|max:999999.99',
            'unit' => 'required|string|max:50',
            'required_by' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'project_id' => __('app.project'),
            'task_id' => __('app.task'),
            'resource_id' => __('app.resource'),
            'resource_type' => __('app.resource_type'),
            'resource_name' => __('app.name'),
            'quantity' => __('app.quantity'),
            'unit' => __('app.unit'),
            'required_by' => __('app.due_date'),
            'description' => __('app.description'),
        ];
    }
}
