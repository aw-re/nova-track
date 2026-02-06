<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ProjectStatusEnum;
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0|max:999999999999.99',
            'status' => ['nullable', new Enum(ProjectStatusEnum::class)],
            'owner_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => __('app.name')]),
            'name.max' => __('validation.max.string', ['attribute' => __('app.name'), 'max' => 255]),
            'end_date.after_or_equal' => __('validation.after_or_equal', ['attribute' => __('app.end_date'), 'date' => __('app.start_date')]),
            'budget.numeric' => __('validation.numeric', ['attribute' => __('app.budget')]),
            'budget.min' => __('validation.min.numeric', ['attribute' => __('app.budget'), 'min' => 0]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('app.name'),
            'description' => __('app.description'),
            'location' => __('app.location'),
            'start_date' => __('app.start_date'),
            'end_date' => __('app.end_date'),
            'budget' => __('app.budget'),
            'status' => __('app.status'),
            'owner_id' => __('app.owner'),
        ];
    }
}
