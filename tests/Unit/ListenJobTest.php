<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Listen;
use App\Notifications\Listened;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenJobTest extends TestCase
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
        ;
        $tags = 'tag1 tag2 tag3';

        /*** act ***/
        $job = new Listen($contact, $tags);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(['tag1', 'tag2', 'tag3'], $contact->hashtags->pluck('tag')->toArray());
        Notification::assertSentTo($contact, Listened::class, function ($notification) use ($contact, $tags) {
            return Listened::getFormattedMessage($contact, $tags) == $notification->getContent($contact);
        });
    }

    /** @test */
    public function contact_add_tags_works()
    {
        /*** arrange ***/
        Notification::fake();
        $contact = factory(Contact::class)
            ->create(['mobile' => '09171234567'])
            ->catch(['tag1', 'tag2', 'tag3'])
        ;
        $tags = 'tag4 tag5';

        /*** act ***/
        $job = new Listen($contact, $tags);
        $job->handle();

        /*** assert ***/
        $this->assertEquals(['tag1', 'tag2', 'tag3', 'tag4', 'tag5'], $contact->hashtags->pluck('tag')->toArray());
        Notification::assertSentTo($contact, Listened::class, function ($notification) use ($contact, $tags) {
            return Listened::getFormattedMessage($contact, $tags) == $notification->getContent($contact);
        });
    }
}
