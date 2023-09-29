<?php

namespace App\Http\Requests\OperationsController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class AddAccountRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'bank' => [
                'sometimes',
                'max:255',
            ],
            'bic' => [
                'sometimes',
                'max:255',
            ],
            'ks' => [
                'sometimes',
                'max:255',
            ],
            'number' => [
                'sometimes',
                'max:255',
            ],
            'currency' => [
                'in:RUB,KZT,BYR',
                'max:255',
            ],
            'balance' => [
                'required',
                'max:255',
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
            ]
        ];
    }
}
