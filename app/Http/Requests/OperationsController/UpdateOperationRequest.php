<?php

namespace App\Http\Requests\OperationsController;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateOperationRequest extends FormRequest
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
                'sometimes',
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
            ],
            'article_id' => [
                'required',
                'integer',
                'exists:articles,id',
                function(string $attribute, mixed $value, Closure $fail){
                    $query = DB::table('articles')->where('id', $value)
                    ->where('user_id', Auth()->id())->exists();
                    if(!$query){
                        $fail('Эта статья вам не принадлежит');
                    }
                }
            ],
            'id' => [
                'required',
                'integer',
                'exists:operations,id',
                function(string $attribute, mixed $value, Closure $fail){
                    $query = DB::table('operations')->where('id', $value)
                    ->where('user_id', Auth()->id())->exists();
                    if(!$query){
                        $fail('Эта операция вам не принадлежит');
                    }
                }
            ]
        ];
    }

    public function getValidatorInstance()
    {
        $this->formatDate();
        return parent::getValidatorInstance();
    }

    protected function formatDate()
    {
        if($this->request->has('date')) {
            $this->merge([
                'date' => Carbon::parse($this->request->get('date'))->format('Y-m-d')
            ]);
        }
    }
}
