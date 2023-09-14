<?php

namespace App\Http\Requests\ProductController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
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
                function(string $attribute, mixed $value, Closure $fail){
                    $lks = Auth()->User()->lk->pluck('id')->all();
                    if(!in_array($value,$lks)){
                        $fail('Указан не верный ЛК');
                    }
                }
                ]
        ];
    }
}
