<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HttpPostTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        $this->artisan('db:seed', ['--class' => 'ContactSeeder']);
    }

    /** @test */
    public function url_must_have_from_to_message_attributes()
    {
        /*** arrange ***/
        $from = '09171234567'; $to = '09187654321'; $message = $this->faker->sentence;

        /*** act ***/
        $response = $this->json($this->method, $this->uri, ['from_number' => $from, 'to_number' => $to, 'content' => $message]);
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(500);
        $this->assertNull(Contact::bearing($from));
    }

    /** @test */
    public function sender_of_any_message_becomes_a_subscriber()
    {
        /*** arrange ***/
        $from = '09171234567'; $to = '09187654321'; $message = $this->faker->sentence;

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        tap(Contact::bearing($from), function ($contact) {
            $this->assertTrue($contact->hasRole('subscriber'));
            $this->assertFalse($contact->hasRole('listener'));
            $this->assertFalse($contact->hasRole('spokesman'));
        });
    }
}
