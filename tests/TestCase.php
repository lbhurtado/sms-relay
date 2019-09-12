<?php

namespace Tests;

use App\Role;
use App\Providers\RouteServiceProvider;
use BeyondCode\Vouchers\Models\Voucher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, WithFaker;

    protected $method = 'POST';
    protected $uri = '/api/sms/relay';

    public function setUp(): void
    {
        parent::setUp();

        (new RouteServiceProvider($this->app))
            ->mapSMSRoutes();//TODO change this to MissiveServiceProvider - change include to require

        $this->faker = $this->makeFaker('en_PH');
    }

    protected function getVoucherCode($name = 'listener')
    {
        $role = Role::where('name', $name)->first();
        $voucher = Voucher::where('model_type', get_class($role))->where('model_id', $role->id)->first();

        return $voucher->code;
    }

    protected function getRandomMobile(int $country = 63, int $prefix = null)
    {
        $prefix = $prefix ?? $this->faker->numberBetween(915,919);
        
        return '+' . $country . $prefix . $this->faker->numberBetween(1000000,9999999);
    }

    protected function getRandomEmail()
    {
        return $this->faker->email;
    }

    protected function getVoucherTemplateMessage($role)
    {
        return "{$this->getVoucherCode($role)} {$this->getRandomEmail()}";
    }

    protected function sleep_after_url(int $micro_seconds = null)
    {
        usleep($micro_seconds ?? env('SLEEP_AFTER_URL', 0));
    }
}
