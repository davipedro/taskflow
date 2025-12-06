<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'string', Rule::in(TaskPriority::values())],
            'deadline' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'O projeto é obrigatório.',
            'project_id.exists' => 'O projeto informado não existe.',
            'user_id.required' => 'O usuário é obrigatório.',
            'user_id.exists' => 'O usuário informado não existe.',
            'title.required' => 'O título é obrigatório.',
            'priority.in' => 'Prioridade inválida.',
        ];
    }
}
