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
        Setting::forget('forwarding.mobiles');
        Setting::forget('forwarding.hashtags');
        Setting::set('forwarding.emails', explode(',', env('FORWARDING_EMAILS', 'lester@hurtado.ph,lbhurtado@gmail.com')));
        Setting::set('forwarding.mobiles', explode(',', env('FORWARDING_SMSS', '09173011987,09189362340')));
        Setting::save();
    }
}
