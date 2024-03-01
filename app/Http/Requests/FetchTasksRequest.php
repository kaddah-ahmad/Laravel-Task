<?php

namespace App\Http\Requests;

use App\Exceptions\ValidatorException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FetchTasksRequest extends FormRequest
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
            'page' => 'integer|nullable',
            'limit' => 'integer|nullable',
            'status' => 'string|nullable',
            'due_date' => 'date|nullable',
            'user_id' => 'integer|nullable',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidatorException($validator->errors());
    }
}
