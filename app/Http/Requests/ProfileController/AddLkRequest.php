<?php

namespace App\Http\Requests\ProfileController;

use App\Models\Lk;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class AddLkRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите название ЛК',
            'name.string' => 'Название должно быть строкой',
            'name.max' => 'Название не должно превышать 255 символов'
        ];
    }
}
