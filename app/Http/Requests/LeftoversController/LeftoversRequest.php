<?php

namespace App\Http\Requests\LeftoversController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class LeftoversRequest extends FormRequest
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
            'lk_id' => [
                'required',
                'integer',
                'exists:lks,id',
                function(string $attribute, mixed $value, Closure $fail){
                    if(Auth()->User()->lk->where('id', $value)->isEmpty()){
                        $fail('Неверный ЛК');
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'lk_id.required' => 'Укажите ЛК',
            'lk_id.integer' => 'ЛК должен быть числом',
            'lk_id.exists' => 'Неверный ЛК'
        ];
    }
}
