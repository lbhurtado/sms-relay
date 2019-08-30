<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\RelayAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RelayTest extends TestCase
{
    use RefreshDatabase;

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(RelayAction::class);
        $this->router->register("{message}", $this->action);
    }

    /** @test */
    public function message_with_hashtags_invokes_relay_action()
    {
        /*** arrange ***/
        $from = '09171234567'; $to = '09182222222'; $message = "#tag1 #tag2 {$this->faker->sentence}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function message_without_hashtags_invokes_relay_action()
    {
        /*** arrange ***/
        $from = '09171234567'; $to = '09182222222'; $message = $this->faker->sentence;

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }
}
