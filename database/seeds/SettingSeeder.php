<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::forget('forwarding.emails');
        Setting::forget('forwarding.sms');
        Setting::set('forwarding.emails', [env('DEFAULT_FORWARDING_EMAIL', 'lester@hurtado.ph')]);
        Setting::set('forwarding.sms', [env('DEFAULT_FORWARDING_SMS', '09173011987')]);
        Setting::save();
    }
}
