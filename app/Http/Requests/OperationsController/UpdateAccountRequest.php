<?php

namespace App\Http\Requests\OperationsController;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateAccountRequest extends FormRequest
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
                'integer',
                'max:1000000000',
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

    public function getValidatorInstance()
    {
        $this->formatBalance();
        return parent::getValidatorInstance();
    }

    protected function formatBalance()
    {
        if($this->request->has('balance')) {
            $val = preg_replace('/\xc2\xa0/', '', $this->request->get('balance'));
            $this->merge([
                'balance' => intval($val)
            ]);
        }
    }
}
