<?php

namespace App\Http\Requests\Products;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $token = $this->bearerToken();

        if (!$token) {
            return false;
        }

        return DB::table('admin_users')
            ->where('api_token', hash('sha256', $token))
            ->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:150',
            ],
            'stock' => [
                'required',
                'integer',
            ],
            'main_price' => [
                'required',
                'decimal:0,2',
            ],
            'final_price' => [
                'nullable',
                'decimal:0,2',
                'lt:main_price',
            ],
            'attributes' => [
                'required',
                'JSON',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
        ];
    }
}
