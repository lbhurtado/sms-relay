<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\ListenAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListenTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'LISTEN';

    protected $tags = ['tag1', 'tag2', 'tag3'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->artisan('db:seed', ['--class' => 'SettingSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(ListenAction::class);
        $this->router->register("{$this->keyword} {tags}", $this->action);
    }

    /** @test */
    public function listen_keyword_space_tags_invokes_listen_action()
    {
        /*** arrange ***/
        $keyword = $this->keyword;
        $tags = $this->getSpaceDelimitedTags();
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$tags}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldHaveReceived('__invoke')->once();
    }

    /** @test */
    public function listen_keyword_alone_does_not_invoke_listen_action()
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
    public function non_listen_keyword_space_tags_does_not_invoke_listen_action()
    {
        /*** arrange ***/
        $keyword = $this->faker->word;
        $tags = $this->getSpaceDelimitedTags();
        $from = '09171234567'; $to = '09182222222'; $message = "{$keyword} {$tags}";

        /*** act ***/
        $this->json($this->method, $this->uri, compact('from', 'to', 'message'));
        $this->sleep_after_url();

        /*** assert ***/
        $this->action->shouldNotHaveReceived('__invoke');
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
