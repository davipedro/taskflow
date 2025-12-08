<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskStatusRequest extends FormRequest
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
            'status' => [
                'required',
                new Enum(TaskStatus::class),
                function ($attribute, $value, $fail) {
                    $task = $this->route('task');
                    $value = TaskStatus::from($value);

                    if ($task && ! $task->canTransitionStatus($value)) {
                        $fail('A transição de status não é permitida.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status é obrigatório.',
        ];
    }
}
