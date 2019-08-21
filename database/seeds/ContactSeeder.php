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
        $contacts = [
            ['09173011987', 'admin'],
            ['09189362340', 'leader'],
        ];
        foreach ($contacts as $contact) {
            $mobile = $contact[0];
            Contact::create(compact('mobile'))->assignRole($contact[1]);
        }
    }
}
