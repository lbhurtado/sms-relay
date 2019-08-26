<?php

namespace Tests\Integration;

use App\Contact;
use App\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\CommandBus\BroadcastAction;
use LBHurtado\Missive\Repositories\ContactRepository;
use LBHurtado\Missive\Models\SMS;

use LBHurtado\Missive\Missive;
use LBHurtado\Missive\Routing\Router;
use App\Notifications\Broadcast;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Feedback;

class BroadcastActionTest extends TestCase
{
    use RefreshDatabase;

    /** @var SMS */
    protected $sms;

    protected $keyword = 'BROADCAST';

    protected $message;

    protected $missive;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->message = $this->faker->sentence;
        $this->sms = SMS::create([
            'from' => '+639171234567',
            'to' => '+639187654321',
            'message' => "{$this->keyword} {$this->message}"
        ]);
        $this->missive = app(Missive::class)->setSMS($this->sms);
        factory(Contact::class)
            ->create(['mobile' => $this->sms->from])
            ->syncRoles('spokesman')
        ;
        factory(Contact::class)->create(['mobile' => '+639111111111']);
        factory(Contact::class)->create(['mobile' => '+639122222222']);
    }

    /** @test */
    public function broadcast_action_works()
    {
        /*** arrange ***/
        Notification::fake();
        $router = tap(new Router($this->missive))->process($this->sms);
        $contacts = app(ContactRepository::class);

        /*** act ***/
        (new BroadcastAction($router, $contacts))->__invoke('', ['message' => $this->message]);

        /*** assert ***/
        $contacts->all()->each(function ($contact) {
            Notification::assertSentTo($contact, Broadcast::class, function ($notification) use ($contact) {
                return $this->message == $notification->getContent($contact);
            });
        });
        tap(Contact::bearing($this->sms->from), function ($spokesman) {
            Notification::assertSentTo($spokesman, Feedback::class, function ($notification) use ($spokesman) {
                return $this->message == $notification->getContent($spokesman);
            });
        });
    }
}
