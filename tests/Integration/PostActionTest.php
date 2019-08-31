<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Notifications\Post;
use LBHurtado\Missive\Missive;
use App\CommandBus\PostAction;
use App\Notifications\Feedback;
use LBHurtado\Missive\Models\SMS;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostActionTest extends TestCase
{
    use RefreshDatabase;

    protected $contact;

    protected $tag = 'scoop';

    /** @var int */
    protected $count;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->createSubscribers($this->count = 2);
    }

    /** @test */
    public function spokesman_post_action_sends_to_all_contacts_with_tags_except_self_but_receives_feedback()
    {
        /*** arrange ***/
        Notification::fake();
        $tag = "#{$this->tag}";
        $message = "{$tag} {$this->faker->sentence}";
        $sms = $this->prepareToPostAs('spokesman');

        /*** act ***/
        app(PostAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        Contact::notBearing($sms->origin->mobile)->each(function ($contact) use ($message, &$i) {
            ++$i;
            Notification::assertSentTo($contact, Post::class, function ($notification) use ($contact, $message) {
                return Post::getFormattedMessage($contact, $message) == $notification->getContent($contact);
            });
        });
        $this->assertEquals($i, $this->count);
        tap(Contact::bearing($sms->origin->mobile), function ($spokesman) use ($message) {
            Notification::assertSentTo($spokesman, Feedback::class, function ($notification) use ($spokesman, $message) {
                return Feedback::getFormattedMessage($spokesman, $message) == $notification->getContent($spokesman);
            });
        });
    }

    /** @test */
    public function listener_broadcast_action_does_nothing()
    {
        /*** arrange ***/
        Notification::fake();
        $tag = "#{$this->tag}";
        $message = "{$tag} {$this->faker->sentence}";
        $sms = $this->prepareToPostAs('listener');

        /*** act ***/
        app(PostAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        Contact::whereNotIn('mobile',[$sms->origin->mobile])->get()->each(function ($contact) use ($message, &$i) {
            ++$i;
            Notification::assertNotSentTo($contact, Post::class);
        });
        tap(Contact::bearing($sms->origin->mobile), function ($listener) use ($message) {
            Notification::assertNotSentTo($listener, Feedback::class);
        });
    }

    /** @test */
    public function subscriber_broadcast_action_does_nothing()
    {
        /*** arrange ***/
        Notification::fake();
        $tag = "#{$this->tag}";
        $message = "{$tag} {$this->faker->sentence}";
        $sms = $this->prepareToPostAs('subscriber');

        /*** act ***/
        app(PostAction::class)->__invoke('', compact('message'));

        /*** assert ***/
        Contact::whereNotIn('mobile',[$sms->origin->mobile])->get()->each(function ($contact) use ($message, &$i) {
            ++$i;
            Notification::assertNotSentTo($contact, Post::class);
        });
        tap(Contact::bearing($sms->origin->mobile), function ($listener) use ($message) {
            Notification::assertNotSentTo($listener, Feedback::class);
        });
    }

    protected function prepareToPostAs(string $role): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($from, $role);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createContact(string $mobile, string $role)
    {
        factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles($role)
        ;

        return $this;
    }

    protected function createSubscribers(int $count)
    {
        for ($i=0; $i < $count; $i++) {
            $mobile = '0918123456'. $i;
            factory(Contact::class)->create(compact('mobile'))->catch($this->tag);
        }

        return $this;
    }
}
