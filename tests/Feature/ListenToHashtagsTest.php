<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenToHashtagsTest extends TestCase
{
    use RefreshDatabase;

    protected $method = 'POST';

    protected $uri = '/api/sms/relay';

    protected $keyword = 'LISTEN';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function listener_sends_listen_command_receives_hashtags()
    {
        /*** arrange ***/
        $from = '09171111111'; $to = '09182222222'; $message = "$this->keyword tag1 tag2 tag3";
        $contact = factory(Contact::class)
            ->create(['mobile' => $from])
            ->syncRoles('listener');

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
        $this->assertEquals(['tag1','tag2','tag3'], $contact->hashtags->pluck('tag')->toArray());

        /*** arrange ***/
        $from = '091233333333'; $to = '09134444444'; $message = '#tag1 the quick brown' ;

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        usleep(1);

        /*** assert ***/
        $response->assertStatus(200);
    }
}
