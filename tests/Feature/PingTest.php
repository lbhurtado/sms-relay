<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\PingAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PingTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'PING';

    protected $router;

    protected $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);
        
        $this->router = app(Router::class);
        $this->action = Mockery::mock(PingAction::class);
        $this->router->register("{$this->keyword}", $this->action);
    }

    /** @test */
    public function ping_keyword_invokes_ping_action()
    {
        /*** arrange ***/
        $space = '   ';
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->keyword}{$space}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function none_ping_keyword_deos_not_invoke_ping_action()
    {
        /*** arrange ***/
        $space = '   ';
        $from = '09171234567'; $to = '09182222222'; $message = "{$this->faker->word}{$space}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
    }
}
