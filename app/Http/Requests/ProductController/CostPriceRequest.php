<?php

namespace App\Http\Requests\ProductController;

use App\Models\WbProduct;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class CostPriceRequest extends FormRequest
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
            'product_id' => [
                            'required',
                            'integer',
                            'exists:wb_products,id',
                            function(string $attribute, mixed $value, Closure $fail){
                                $lks = Auth()->User()->lk->pluck('id')->all();
                                $product = WbProduct::findOrFail($value);

                                if(!in_array($product->lk_id,$lks)){
                                    $fail('Ошибка');
                                }
                            }
                        ],
            'price' => 'required|integer'
        ];
    }
}
