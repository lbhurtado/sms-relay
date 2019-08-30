<?php

namespace Tests\Integration;

use App\Contact;
use Tests\TestCase;
use App\Jobs\Listen;
use LBHurtado\Missive\Missive;
use App\CommandBus\ListenAction;
use LBHurtado\Missive\Models\SMS;
use Illuminate\Support\Facades\Bus;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var array */
    protected $tags = ['tag1', 'tag2', 'tag3'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /** @test */
    public function listener_listen_action_receives_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToListenAs('listener');
        $tags = $this->getSpaceDelimitedTags();

        /*** act ***/
        app(ListenAction::class)->__invoke('', compact('tags'));

        /*** assert ***/
        Bus::assertDispatched(Listen::class, function ($job) use ($tags, $sms) {
            return $job->contact === $sms->origin && $job->tags == $tags;
        });
    }

    /** @test */
    public function spokesman_listen_action_receives_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToListenAs('spokesman');
        $tags = $this->getSpaceDelimitedTags();

        /*** act ***/
        app(ListenAction::class)->__invoke('', compact('tags'));

        /*** assert ***/
        Bus::assertDispatched(Listen::class, function ($job) use ($tags, $sms) {
            return $job->contact === $sms->origin && $job->tags == $tags;
        });
    }

    /** @test */
    public function subscriber_listen_action_does_not_receive_hashtags()
    {
        /*** arrange ***/
        Bus::fake();
        $sms = $this->prepareToListenAs('subscriber');
        $tags = $this->getSpaceDelimitedTags();

        /*** act ***/
        app(ListenAction::class)->__invoke('', compact('tags'));

        /*** assert ***/
        Bus::assertNotDispatched(Listen::class);
    }

    protected function prepareToListenAs(string $role): \LBHurtado\Missive\Models\SMS
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

    /**
     * @return string
     * i.e "tag1 tag2 tag3"
     */
    protected function getSpaceDelimitedTags()
    {
        return implode(' ', $this->tags);
    }
}
