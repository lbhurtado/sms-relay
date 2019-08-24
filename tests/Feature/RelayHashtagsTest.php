<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use App\Notifications\MailHashtags;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelayHashtagsTest extends TestCase
{
    use RefreshDatabase;

    protected $method = 'POST';

    protected $uri = '/api/sms/relay';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function hashtags_get_relayed()
    {
        /*** arrange ***/
        Notification::fake();
        $contact = Contact::create(['mobile' => '09109999999'])
            ->syncRoles('listener')
            ->setEmail('test.info.co')
            ->catch(['info'])
            ;

        $from = '09171111111'; $to = '09182222222'; $message = "#info testing";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
        Notification::assertSentTo($contact, MailHashtags::class);
    }
}
