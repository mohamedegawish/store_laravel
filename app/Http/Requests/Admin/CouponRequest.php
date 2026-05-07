<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $couponId = $this->route('coupon')?->id;

        return [
            'code'                => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'name_ar'             => 'required|string|max:255',
            'name_en'             => 'required|string|max:255',
            'type'                => 'required|in:fixed,percentage',
            'value'               => 'required|numeric|min:0|' . ($this->type === 'percentage' ? 'max:100' : ''),
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'user_limit'          => 'nullable|integer|min:1',
            'starts_at'           => 'nullable|date',
            'expires_at'          => 'nullable|date|after:starts_at',
            'is_active'           => 'nullable|boolean',
        ];
    }
}
