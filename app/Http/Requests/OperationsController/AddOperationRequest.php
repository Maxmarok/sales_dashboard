<?php

namespace App\Http\Requests\OperationsController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class AddOperationRequest extends FormRequest
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
            'art' => [
                'max:255',
            ],
            'value' => [
                'required',
                'integer',
                'max:100000000',
            ],
            'date' => [
                'required',
                'date',
            ],
            'description' => [
                'required',
                'string',
                'max:255',
            ],
            'type' => [
                'required',
                'in:consume,profit',
            ],
            'account_id' => [
                'required',
                'integer',
                'exists:bank_accounts,id',
                function(string $attribute, mixed $value, Closure $fail){
                    $query = DB::table('bank_accounts')->where('id', $value)
                    ->where('user_id', Auth()->id())->exists();
                    if(!$query){
                        $fail('Этот счет вам не принадлежит');
                    }
                }
            ]
        ];
    }
}
