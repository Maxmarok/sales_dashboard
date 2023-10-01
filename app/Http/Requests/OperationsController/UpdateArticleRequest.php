<?php

namespace App\Http\Requests\OperationsController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class UpdateArticleRequest extends FormRequest
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
            'description' => [
                'sometimes',
                'max:255',
            ],
            'type' => [
                'required',
                'in:main,buying,invest,credit,profit',
            ],
            'id' => [
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
            ]
        ];
    }
}
