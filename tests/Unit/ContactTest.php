<?php

namespace Tests\Unit;

use App\Contact;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected $mobile = '09108888888';
    protected $contact;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
        $this->contact = factory(Contact::class)->create(['mobile' => $this->mobile]);
    }

    /** @test */
    public function contact_is_initially_a_subscriber()
    {
        /*** assert ***/
        $this->assertTrue($this->contact->hasRole('subscriber'));
        $this->assertFalse($this->contact->hasRole('listener'));
        $this->assertFalse($this->contact->hasRole('spokesman'));
    }

    /** @test */
    public function contact_has_no_email_initially()
    {
        /*** assert ***/
        $this->assertNull($this->contact->email);
    }

    /** @test */
    public function contact_has_no_tags_initially()
    {
        /*** assert ***/
        $this->assertEmpty($this->contact->hashtags);
    }

    /** @test */
    public function contact_can_be_searched_with_mobile()
    {
        /*** arrange ***/
        $contact = Contact::bearing($this->mobile);

        /*** assert ***/
        $this->assertTrue($this->contact->is($contact));
    }
}
