<?php

namespace App\Http\Requests\ProfileController;

use App\Models\Lk;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class ChangeApiKeyRequest extends FormRequest
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
            'marketplace' => 'required|string|in:WB,OZ,YA',
            'key' => 'required|string',
            'type' => [
                'required',
                'string',
                'in:statistic,standard,ad',
                ],
            'lk_id' => [
                'required',
                'integer',
                'exists:lks,id',
                function(string $attribute, mixed $value, Closure $fail){
                    $query = DB::table('lks')->where('id', $value)
                    ->where('user_id', Auth()->id())->exists();
                    if(!$query){
                        $fail('Этот ЛК вам не принадлежит');
                    }
                }
            ],
            'id' => [
                'required',
                'integer',
                'exists:api_keys,id'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'marketplace.required' => 'Выберите маркетплейс',
            'marketplace.string' => 'Маркетплейс должен быть строкой',
            'marketplace.in' => 'Указан неверный маркетплейс',

            'key.required' => 'Введите API-ключ',
            'key.string' => 'API-ключ должен быть строкой',

            'type.required' => 'Укажите тип ключа',
            'type.string' => 'Тип ключа должен быть строкой',
            'type.in' => 'Неверный тип ключа',

            'lk.required' => 'Выберите ЛК',
            'lk.integer' => 'ЛК должен быть числом',
            'lk.exists' => 'Данного ЛК не существует'
        ];
    }
}
