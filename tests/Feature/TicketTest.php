<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\TicketAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'TICKET';

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $regex_code = ''; $regex_email = '';
        $this->router = app(Router::class);
        $this->action = Mockery::mock(TicketAction::class);
        $this->router->register("{$this->keyword} {title} {message}", $this->action);
    }

    /** @test */
    public function ticket_space_title__space_messge_invokes_ticket_action()
    {
        /*** arrange ***/
        $title = $this->faker->title;
        $message = $this->faker->sentence;
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->keyword} {$title} {$message}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function ticket_alone_does_not_invoke_redeem_action()
    {
        /*** arrange ***/
        $space = ' ';
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->keyword}{$space}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
