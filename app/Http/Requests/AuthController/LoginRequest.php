<?php

namespace App\Http\Requests\AuthController;

use App\Models\User;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'max:255',
                // function (string $attribute, mixed $value, Closure $fail) {
                //     abort_if(!User::where('email', $value)->exists(), 403, 'Пользователя с таким email не существует');
                // }
            ],
            'password' => [
                'required',
                'string',
                // function(string $attribute, mixed $value, Closure $fail) {
                //     $user = User::where('email', $this->email)->first();
                //     abort_if(!$user || !Hash::check($value, $user->password), 403, 'Неверный пароль');
                // }
                ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Введите Email',
            'email.email' => 'Неверный формат Email',
            'email.max' => 'Максимальная длина Email 255',

            'password.required' => 'Введите пароль',
            'password.string' => 'Пароль должен быть строкой'
        ];
    }
}
