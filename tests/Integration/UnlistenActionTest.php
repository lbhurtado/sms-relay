<?php

namespace Tests\Integration;

use TypeError;
use App\Contact;
use Tests\TestCase;
use App\Jobs\Unlisten;
use LBHurtado\Missive\Missive;
use LBHurtado\Missive\Models\SMS;
use App\CommandBus\UnlistenAction;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlistenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var array */
    protected $tags = ['tag1', 'tag2'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function listener_listen_action_removes_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToUnlistenAs('listener', ['tag1', 'tag2', 'tag3', 'tag4']);
        $tags = $this->getSpaceDelimitedTags();

        /*** act ***/
        app(UnlistenAction::class)('UNLISTEN', compact('tags'));

        /*** assert ***/
        Bus::assertDispatched(Unlisten::class, function ($job) use ($tags, $sms) {
            return $job->contact === $sms->origin && $job->tags == $tags;
        });
    }

    /** @test */
    public function spokesman_listen_action_removes_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToUnlistenAs('spokesman', ['tag1', 'tag2', 'tag3', 'tag4']);
        $tags = $this->getSpaceDelimitedTags();

        /*** act ***/
        app(UnlistenAction::class)('UNLISTEN', compact('tags'));

        /*** assert ***/
        Bus::assertDispatched(Unlisten::class, function ($job) use ($tags, $sms) {
            return $job->contact === $sms->origin && $job->tags == $tags;
        });
    }

    /** @test */
    public function subscriber_listen_action_does_not_remove_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToUnlistenAs('subscriber', ['tag1', 'tag2', 'tag3', 'tag4']);
        $tags = $this->getSpaceDelimitedTags();
        $this->expectException(TypeError::class);

        /*** act ***/
        app(UnlistenAction::class)('UNLISTEN', compact('tags'));

        /*** assert ***/
        Bus::assertNotDispatched(Unlisten::class);
    }

    protected function prepareToUnlistenAs(string $role, array $hashtags): \LBHurtado\Missive\Models\SMS
    {
        $from = '+639191234567';
        $sms = factory(SMS::class)->create(compact('from'));
        $this->createContact($from, $role, $hashtags);

        $missive = app(Missive::class)->setSMS($sms);
        (new Router($missive))->process($sms);

        return $sms;
    }

    protected function createContact(string $mobile, string $role, array $hashtags)
    {
        factory(Contact::class)
            ->create(compact('mobile'))
            ->syncRoles($role)
            ->catch($hashtags)
        ;

        return $this;
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
