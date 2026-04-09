<?php

namespace App\Services\Backend;

use App\Models\Coupon;

class CouponService
{
    public function upsert(array $data, ?Coupon $coupon = null): Coupon
    {
        $coupon ??= new Coupon();
        $coupon->fill($data);
        $coupon->save();

        return $coupon;
    }
}

