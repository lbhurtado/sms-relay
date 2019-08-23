<?php

namespace Tests;

use App\Role;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->makeFaker('en_PH');
    }

    protected function getRandomMobile()
    {
        do {
            try {
                $mobile =  phone($this->faker->mobileNumber, 'PH')->formatE164();
            }
            catch (\Exception $e) {

            }
        }
        while (! isset($mobile));

        return $mobile;
    }

    protected function getVoucherCode($name = 'listener')
    {
        $role = Role::where('name', $name)->first();
        $voucher = Voucher::where('model_type', get_class($role))->where('model_id', $role->id)->first();

        return $voucher->code;
    }

    protected function getRandomEmail()
    {
        return $this->faker->email;
    }

    protected function getVoucherTemplateMessage($role)
    {
        return "{$this->getVoucherCode($role)} {$this->getRandomEmail()}";
    }
}
