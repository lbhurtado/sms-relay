<?php

namespace App\Jobs;

use App\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RedeemCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Contact */
    public $contact;

    /** @var string */
    public $code;

    /**
     * RedeemCode constructor.
     * @param Contact $contact
     * @param string $code
     */
    public function __construct(Contact $contact, string $code)
    {
        $this->contact = $contact;
        $this->code = $code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        tap($this->getVoucher()->model, function ($role) {
            $this->contact->syncRoles($role);
        });
    }

    protected function getVoucher(): \BeyondCode\Vouchers\Models\Voucher
    {
        return $this->contact->redeemCode($this->code);
    }
}
