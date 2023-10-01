<?php

namespace App\Http\Requests\OperationsController;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class AddArticleRequest extends FormRequest
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
            'article_type' => [
                'required',
                'in:consume,profit',
            ],
            'type' => [
                'required',
                'in:main,buying,invest,credit,profit',
            ],
        ];
    }
}
