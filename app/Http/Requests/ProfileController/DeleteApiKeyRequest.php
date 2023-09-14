<?php

namespace App\Http\Requests\ProfileController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class DeleteApiKeyRequest extends FormRequest
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
            'key_id' => [
                'required',
                'integer',
                'exists:api_keys,id',
                function(string $attribute, mixed $value, Closure $fail){
                    if(Auth()->user()->apiKeys()->where('id', $value)->isEmpty()){
                        $fail('Указанный ключ вам не принадлежит');
                    }
                }
                ]
        ];
    }

    public function messages(): array
    {
        return [
            'key_id.required' => 'Укажите ключ',
            'key_id.integer' => 'Ключ должен быть числом',
            'key_id.exists' => 'Указанного ключа не существует'
        ];
    }
}
