<?php

namespace App\Http\Requests;

use App\Exceptions\ValidatorException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'user_id' => 'required|nullable|exists:users,id',
            'title' => 'required|string|nullable',
            'description' => 'required|string|nullable',
            'due_date' => 'required|nullable|date|after_or_equal:today',
            'status' => 'required|string|nullable'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidatorException($validator->errors());
    }
}
