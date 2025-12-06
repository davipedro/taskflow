<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskPriorityRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'priority' => ['required', 'string', Rule::in(TaskPriority::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.in' => 'Prioridade inválida.',
        ];
    }
}
