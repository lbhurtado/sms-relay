<?php

use App\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mobiles = Setting::get('forwarding.mobiles');
        $i = 1;
        foreach ($mobiles as $mobile) {
            $handle = "SMS Forward " .  $i++;
            Contact::create(compact('mobile', 'handle'))->syncRoles('forwarder');
        }
    }

    protected function deprecate_seed()
    {
        $records = [
            ['09171111111', 'spokesman'],
            ['09182222222', 'listener', '09182222222@lgu.gov.ph', ['*']],
            ['09183333333', 'listener', '09183333333@lgu.gov.ph', ['inquire']],
            ['09184444444', 'listener', '09184444444@lgu.gov.ph', ['inquire', 'complain']],
            ['09185555555', 'listener', '09185555555@lgu.gov.ph', ['inquire', 'scoop']],
            ['09186666666', 'listener', '09186666666@lgu.gov.ph', ['inquire', 'followup']],
            ['09187777777', 'listener', '09187777777@lgu.gov.ph', ['inquire', 'appointment']],
        ];

        foreach ($records as $record) {
            $mobile = $record[0];
            $contact = Contact::create(compact('mobile'))->assignRole($record[1]);
            if ($email = Arr::get($record,2)) {
                $contact->extra_attributes['email'] = $email;
            }
            if ($hashtags = Arr::get($record,3)) {
                $tags = [];
                foreach ($hashtags as $tag) {
                    $tags[] = ['tag' => $tag];
                }
                $contact->hashtags()->createMany($tags);
            }
            $contact->save();
        }
    }
}
