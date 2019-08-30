<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\CommandBus\UnlistenAction;
use LBHurtado\Missive\Routing\Router;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnlistenTest extends TestCase
{
    use RefreshDatabase;

    protected $keyword = 'UNLISTEN';

    protected $tags = ['tag1', 'tag2'];

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);

        $this->router = app(Router::class);
        $this->action = Mockery::mock(UnlistenAction::class);
        $this->router->register("{$this->keyword} {tags}", $this->action);
    }

    /** @test */
    public function unlisten_keyword_space_tags_invokes_unlisten_action()
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
    public function unlisten_keyword_alone_does_not_invoke_unlisten_action()
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
    public function non_unlisten_keyword_space_tags_does_not_invoke_unlisten_action()
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
