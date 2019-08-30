<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Unlisten;
use App\Notifications\Unlistened;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlistenJobTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function any_contact_regardless_of_role_listen_to_tags_works()
    {
        /*** arrange ***/
        Notification::fake();
        $contact = factory(Contact::class)
            ->create(['mobile' => '09171234567'])
            ->catch(['tag1', 'tag2', 'tag3', 'tag4'])
        ;
        $tags = 'tag1 tag2';

        /*** act ***/
        $job = new Unlisten($contact, $tags);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(['tag3', 'tag4'], $contact->hashtags->pluck('tag')->toArray());
        Notification::assertSentTo($contact, Unlistened::class, function ($notification) use ($contact, $tags) {
            return Unlistened::getFormattedMessage($contact, $tags) == $notification->getContent($contact);
        });
    }
}
