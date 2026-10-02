<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Qualification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PortfolioCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_uses_content_from_the_cms(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('sharing and keeping.')
            ->assertSee('Publication design');
    }

    public function test_an_enquiry_is_saved_and_a_notification_is_sent(): void
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'name' => 'Example Client',
            'email' => 'client@example.com',
            'subject' => 'New website',
            'message' => 'I would like to discuss a new website for my business.',
            'website' => '',
        ]);

        $response->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas(ContactMessage::class, [
            'email' => 'client@example.com',
            'subject' => 'New website',
            'status' => 'new',
        ]);
        Mail::assertSentCount(1);
    }

    public function test_portfolio_pages_and_project_case_studies_are_accessible(): void
    {
        $project = Project::query()->create(['title'=>'Sample Directory','slug'=>'sample-directory','category'=>'Directory','type'=>'publication','summary'=>'A sample publication.','accent'=>'plum','sort_order'=>1,'is_published'=>true]);

        $this->get('/work')->assertOk()->assertSee($project->title);
        $this->get(route('work.show', $project))->assertOk()->assertSee($project->title);
        $this->get('/about')->assertOk();
        $this->get('/services')->assertOk();
        $this->get('/contact')->assertOk()->assertSee('Send enquiry');
        $this->get('/privacy')->assertOk()->assertSee('What is collected');
    }

    public function test_unpublished_projects_do_not_appear(): void
    {
        Project::query()->create(['title'=>'Private Draft','slug'=>'private-draft','category'=>'Draft','type'=>'publication','summary'=>'Not public.','accent'=>'plum','sort_order'=>1,'is_published'=>false]);
        $this->get('/work')->assertDontSee('Private Draft');
    }

    public function test_the_honeypot_rejects_automated_submissions(): void
    {
        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'This automated message is definitely long enough.',
            'website' => 'spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }
}
