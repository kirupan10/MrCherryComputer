<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class StoreProductRequest extends FormRequest
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
        /** @var User|null $user */
        $user = auth()->user();
        $shop = $user instanceof User ? $user->getActiveShop() : null;
        $shopId = $shop ? $shop->id : null;

        $buyingPriceRule = $user instanceof User && !$user->isEmployee()
            ? 'required|numeric|min:0|lt:selling_price'
            : 'nullable|numeric|min:0|lt:selling_price';

        $slugRule = $shopId
            ? 'required|unique:products,slug,NULL,id,shop_id,' . $shopId
            : 'required|unique:products,slug';

        $codeRule = $shopId
            ? ['required','regex:/^PRD\d{5}$/','unique:products,code,NULL,id,shop_id,' . $shopId]
            : ['required','regex:/^PRD\d{5}$/','unique:products,code'];

        return [
            'product_image'     => 'image|file|max:2048',
            'name'              => 'required|string',
            'slug'              => $slugRule,
            'code'              => $codeRule,
            'category_id'       => 'nullable|integer',
            'unit_id'           => 'required|integer',
            'quantity'          => 'required|numeric|min:0',
            'buying_price'      => $buyingPriceRule,
            'selling_price'     => 'required|numeric|min:0',
            'quantity_alert'    => 'required|numeric|min:0',
            'warranty_id'       => 'nullable|integer',
            'notes'             => 'nullable|max:1000'
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'buying_price.required' => 'Buying price is required.',
            'buying_price.lt' => 'Buying price must be less than selling price. Please check your product pricing.',
        ];
    }

    protected function prepareForValidation(): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $shop = $user instanceof User ? $user->getActiveShop() : null;
        $shopId = $shop ? $shop->id : null;
        $defaultUnitId = $this->resolveDefaultUnitId($shopId);

        $this->merge([
            'slug' => Str::slug($this->name, '-'),
            // Generate shop-scoped codes like PRD00001, PRD00002 within the same shop
            'code' => \App\Models\Product::generateSku($shopId),
            'quantity' => $this->quantity ?? 1,
            'quantity_alert' => $this->quantity_alert ?? 1,
            'buying_price' => $this->buying_price ?? 0,
            'unit_id' => $this->unit_id ?? $defaultUnitId,
            'warranty_id' => $this->warranty_id,
            'notes' => $this->notes,
        ]);
    }

    private function resolveDefaultUnitId(?int $shopId): ?int
    {
        $unit = Unit::query()
            ->withoutGlobalScope(\App\Scopes\ShopScope::class)
            ->where('slug', 'piece')
            ->where(function ($query) use ($shopId) {
                if ($shopId) {
                    $query->where('shop_id', $shopId)->orWhereNull('shop_id');
                } else {
                    $query->whereNull('shop_id');
                }
            })
            ->orderByRaw('shop_id IS NULL')
            ->orderBy('id')
            ->first();

        return $unit?->id;
    }

}
