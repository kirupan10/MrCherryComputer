<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCustomerRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'email' => 'nullable|email|max:50',
            'phone' => 'required|string|max:25',
            'date_of_birth' => 'nullable|date|before:today',
            'address' => 'nullable|string|max:100',
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

            if ($email) {
                $exists = \App\Models\Customer::where('email', $email)
                    ->where('shop_id', $shopId)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('email', 'A customer with this email already exists.');
                }
            }

            if ($normalizedPhone) {
                $exists = \App\Models\Customer::whereRaw('REPLACE(phone, " ", "") = ?', [$normalizedPhone])
                    ->where('shop_id', $shopId)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('phone', 'A customer with this phone number already exists (ignoring spaces).');
                }
            }
        });
    }

    /**
     * Handle a failed validation attempt for AJAX requests
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
