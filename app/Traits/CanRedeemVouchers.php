<?php

namespace App\Traits;

use Vouchers;
use BeyondCode\Vouchers\Models\Voucher;
use App\Events\{SMSRelayEvents, SMSRelayEvent};
use BeyondCode\Vouchers\Exceptions\VoucherExpired;
use BeyondCode\Vouchers\Exceptions\VoucherIsInvalid;
use BeyondCode\Vouchers\Exceptions\VoucherAlreadyRedeemed;

trait CanRedeemVouchers
{
    /**
     * @param string $code
     * @return mixed
     */
    public function redeemCode(string $code)
    {
        $voucher = Vouchers::check($code);

        if ($voucher->users()->wherePivot('contact_id', $this->attributes['id'])->exists()) {
            throw VoucherAlreadyRedeemed::create($voucher);
        }
        if ($voucher->isExpired()) {
            throw VoucherExpired::create($voucher);
        }

        $this->vouchers()->attach($voucher, [
            'redeemed_at' => now()
        ]);

        event(SMSRelayEvents::REDEEMED, (new SMSRelayEvent($this))->setVoucher($voucher));

        return $voucher;
    }

    /**
     * @param Voucher $voucher
     * @return mixed
     */
    public function redeemVoucher(Voucher $voucher)
    {
        return $this->redeemCode($voucher->code);
    }

    /**
     * @return mixed
     */
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class)->withPivot('redeemed_at');
    }
}
