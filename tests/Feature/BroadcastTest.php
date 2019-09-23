<?php

namespace Tests\Feature;

use Mockery;
use App\Contact;
use Tests\TestCase;
use App\CommandBus\BroadcastAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BroadcastTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'BROADCAST';

    protected $router;

    protected $action;

    /** @var Contact */
    protected $spokesman, $listener, $subscriber1, $subscriber2, $subscriber3;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(BroadcastAction::class);
        $this->router->register("{$this->keyword} {message}", $this->action);
    }

    /** @test */
    public function broadcast_keyword_space_message_invokes_broadcast_action()
    {
        /*** arrange ***/
        $keyword = $this->keyword;
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$this->faker->sentence}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function broadcast_keyword_alone_does_not_invoke_broadcast_action()
    {
        /*** arrange ***/
        $keyword = $this->keyword;
        $space = '  ';
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword}{$space}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }

    /** @test */
    public function non_broadcast_keyword_space_message_does_not_invoke_broadcast_action()
    {
        /*** arrange ***/
        $keyword = $this->faker->word;
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$this->faker->sentence}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
