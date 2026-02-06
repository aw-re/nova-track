<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\ReportStatusEnum;
use App\Enums\ReportTypeEnum;
use Illuminate\Validation\Rules\Enum;

class StoreReportRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:50000',
            'type' => ['required', new Enum(ReportTypeEnum::class)],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'project_id' => __('app.project'),
            'title' => __('app.title'),
            'content' => __('app.description'),
            'type' => __('app.report_type'),
        ];
    }
}
