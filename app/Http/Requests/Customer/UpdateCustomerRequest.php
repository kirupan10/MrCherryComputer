<?php

namespace App\Http\Requests\Customer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'name' => [
                'nullable',
                'string',
                'max:50'
            ],
            'email' => [
                'nullable',
                'email',
                'max:50',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:25',
            ],
            'address' => [
                'nullable',
                'string',
                'max:100'
            ],
            'account_holder' => [
                'nullable',
                'string',
                'max:100'
            ],
            'account_number' => [
                'nullable',
                'string',
                'max:50'
            ],
            'bank_name' => [
                'nullable',
                'string',
                'max:100'
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();
            $shopId = $user && $user->getActiveShop() ? $user->getActiveShop()->id : null;
            $email = $this->input('email');
            $phone = $this->input('phone');
            $normalizedPhone = $phone ? preg_replace('/\s+/', '', $phone) : null;
            $customerId = $this->customer ? (is_object($this->customer) ? $this->customer->id : $this->customer) : null;

            if ($email) {
                $exists = \App\Models\Customer::where('email', $email)
                    ->where('shop_id', $shopId)
                    ->where('id', '!=', $customerId)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('email', 'A customer with this email already exists.');
                }
            }

            if ($normalizedPhone) {
                $exists = \App\Models\Customer::whereRaw('REPLACE(phone, " ", "") = ?', [$normalizedPhone])
                    ->where('shop_id', $shopId)
                    ->where('id', '!=', $customerId)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('phone', 'A customer with this phone number already exists (ignoring spaces).');
                }
            }
        });
    }
}
