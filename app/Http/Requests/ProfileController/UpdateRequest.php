<?php

namespace App\Http\Requests\ProfileController;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => ['required',
                        'email',
                        'max:255',
                        Rule::unique('users')->ignore(Auth()->id())
                    ],
            'password' => 'nullable|string|confirmed|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Введите имя',
            'name.string' => 'Имя должно быть строкой',
            'name.max' => 'Длина имя не должна превышать 255 символов',

            'email.required' => 'Введите email',
            'email.email' => 'Неверный формат Email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'email.max' => 'Длина Email не должна превышать 255 символов',

            'password.string' => 'Пароль должен быть строкой',
            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Минимальная длина пароля 8 символов'
        ];
    }
}
