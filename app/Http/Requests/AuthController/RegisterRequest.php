<?php

namespace App\Http\Requests\AuthController;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'      => 'required|string|max:255',
            'phone'     => 'required|phone',
            'email'     => 'required|email|unique:users,email|max:255',
            'password'  => 'required|string|min:8'
        ];
    }

    public function getValidatorInstance()
    {
        $this->formatPhone();
        return parent::getValidatorInstance();
    }

    protected function formatPhone()
    {
        if($this->request->has('phone')) {
            $this->merge([
                'phone' => str_replace(['-','_',' ','(',')'], '', $this->request->get('phone'))
            ]);
        }
    }
}
