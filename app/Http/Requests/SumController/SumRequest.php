<?php

namespace App\Http\Requests\SumController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class SumRequest extends FormRequest
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
            'dateFrom' => 'required|date_format:"Y-m-d"',
            'dateTo' => 'required|date_format:"Y-m-d"',
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
            'dateFrom.required' => 'Введите начальную дату',
            'dateFrom.date_format' => 'Формат начальной даты должен быть Y-m-d',
            'dateTo.required' => 'Введите конечную дату',
            'dateTo.date_format' => 'Формат конечной даты должен быть Y-m-d',
            'lk_id.required' => 'Укажите личный кабинет',
            'lk_id.integer' => 'Личный кабинет должен быть числом',
            'lk_id.exists' => 'Переданного личного кабинета не существует'
        ];
    }
}
