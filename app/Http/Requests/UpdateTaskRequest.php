<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.string' => 'O título deve ser um texto válido.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.string' => 'A descrição deve ser um texto válido.',
            'status.required' => 'O status da tarefa é obrigatório.',
            'status.in' => 'O status informado é inválido.',
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.in' => 'A prioridade selecionada é inválida.',
            'deadline.date' => 'A data de prazo deve ser uma data válida.',
            'completed_at.date' => 'A data de conclusão deve ser uma data válida.',
        ];
    }
}
