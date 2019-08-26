<?php

namespace Tests\Feature;

use App\Contact;
use Tests\TestCase;
use App\Notifications\Listened;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'LISTEN';

    protected $contact;

    protected $tags = ['tag1', 'tag2', 'tag3'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->contact = factory(Contact::class)->create(['mobile' => '09171234567']);
    }

    /** @test */
    public function listener_sends_listen_command_receives_hashtags()
    {
        /*** arrange ***/
        Notification::fake();
        $this->contact->syncRoles('listener');

        $from = $this->contact->mobile; $to = '09182222222'; $message = "{$this->keyword} {$this->getSpaceDelimitedTags()}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        $this->assertEquals($this->tags, $this->contact->hashtags->pluck('tag')->toArray());
        Notification::assertSentTo($this->contact, Listened::class);
    }

    /** @test */
    public function subscriber_sends_listen_command_does_not_receive_hashtags()
    {
        /*** arrange ***/
        Notification::fake();
        $this->contact->syncRoles('subscriber');

        $from = $this->contact->mobile; $to = '09182222222'; $message = "{$this->keyword} {$this->getSpaceDelimitedTags()}";

        /*** act ***/
        $response = $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $response->assertStatus(200);
        $this->assertEmpty($this->contact->hashtags->pluck('tag')->toArray());
        Notification::assertNotSentTo($this->contact, Listened::class);
    }

    /**
     * @return string
     * i.e "tag1 tag2 tag3"
     */
    protected function getSpaceDelimitedTags()
    {
        return implode(' ', $this->tags);
    }
}
