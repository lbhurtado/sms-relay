<?php

namespace Tests\Feature;

use Mockery;
use App\Contact;
use Tests\TestCase;
use App\CommandBus\PostAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'POST';

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
        $this->action = Mockery::mock(PostAction::class);
        $this->router->register("{$this->keyword} {message}", $this->action);
    }

    /** @test */
    public function post_keyword_space_message_invokes_post_action()
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
    public function post_keyword_alone_does_not_invoke_post_action()
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
    public function non_post_keyword_space_message_does_not_invoke_post_action()
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
