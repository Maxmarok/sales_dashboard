<?php

namespace App\Http\Requests\RevenueController;

use Illuminate\Foundation\Http\FormRequest;

class NetProfitRequest extends FormRequest
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
            'dateFrom' => 'required|date_format:"Y-m-d"'
        ];
    }

    public function messages(): array
    {
        return [
            'dateFrom.required' => 'Введите начальную дату',
            'dateFrom.date_format' => 'Формат начальной даты должен быть Y-m-d'
        ];
    }
}
