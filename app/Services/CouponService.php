<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;

class CouponService
{
    public function validate(string $code, float $orderTotal, ?int $userId = null): array
    {
        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => __('store.coupon_not_found')];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => __('store.coupon_expired_or_inactive')];
        }

        if ($orderTotal < $coupon->min_order_amount) {
            return [
                'valid'   => false,
                'message' => __('store.coupon_min_order', ['amount' => $coupon->min_order_amount]),
            ];
        }

        // Check per-user usage limit
        if ($userId && $coupon->user_limit) {
            $userUsages = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();
            if ($userUsages >= $coupon->user_limit) {
                return ['valid' => false, 'message' => __('store.coupon_user_limit_reached')];
            }
        }

        $discount = $coupon->calculateDiscount($orderTotal);

        return [
            'valid'    => true,
            'coupon'   => $coupon,
            'discount' => $discount,
            'message'  => __('store.coupon_applied', ['discount' => $discount]),
        ];
    }
}
