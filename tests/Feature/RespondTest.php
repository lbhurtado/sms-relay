<?php

namespace Tests\Feature;

use Mockery;
use App\Contact;
use Tests\TestCase;
use App\CommandBus\RespondAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RespondTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'RESPOND';

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
        $this->action = Mockery::mock(RespondAction::class);
        $this->router->register("{$this->keyword} {ticket_id=\w+} {message}", $this->action);
    }

    /** @test */
    public function respond_keyword_space_ticket_id_space_message_invokes_respond_action()
    {
        /*** arrange ***/
        $keyword = $this->keyword;
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$this->faker->word} {$this->faker->sentence}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function respond_keyword_alone_does_not_invoke_respond_action()
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
    public function non_respond_keyword_space_ticket_id_space_message_does_not_invoke_respond_action()
    {
        /*** arrange ***/
        $keyword = $this->faker->word;
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$this->faker->word} {$this->faker->sentence}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
