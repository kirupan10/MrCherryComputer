<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'product_image'     => 'image|file|max:2048',
            'name'              => 'required|string',
            'slug'              => [
                Rule::unique('products')->ignore($this->product)->where('shop_id', auth()->user()->getActiveShop()?->id)
            ],
            'category_id'       => 'nullable|integer',
            'unit_id'           => 'required|integer',
            'quantity'          => 'required|numeric|min:0',
            'buying_price'      => 'nullable|numeric|min:0|lt:selling_price',
            'selling_price'     => 'required|numeric|min:0',
            'quantity_alert'    => 'required|numeric|min:0',
            'notes'             => 'nullable|max:1000'
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'buying_price.lt' => 'Buying price must be less than selling price. Please check your product pricing.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name, '-'),
        ]);
    }
}
