<?php

namespace Tests\Feature;

use App\Livewire\Landing\ContactForm;
use App\Models\Career;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class PhaseThreePortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::clear();
    }

    public function test_public_case_study_is_accessible_by_slug(): void
    {
        $user = User::factory()->create();

        $project = Project::query()->create([
            'user_id' => $user->id,
            'name' => 'Professional Portfolio',
            'slug' => 'professional-portfolio',
            'status' => 'completed',
            'is_published' => true,
            'case_study_published' => true,
            'role' => 'Full-stack Engineer',
            'summary' => 'A concise project summary.',
            'problem' => 'The previous experience did not communicate professional value.',
            'solution' => 'The information architecture was rebuilt around case studies.',
            'outcome' => 'Visitors can now evaluate decisions and outcomes.',
            'tags' => ['Laravel', 'Livewire'],
            'likes' => 0,
        ]);

        $this->get(route('projects.show', $project->slug))
            ->assertOk()
            ->assertSee('Professional Portfolio')
            ->assertSee('Full-stack Engineer')
            ->assertSee('The previous experience did not communicate professional value.');
    }

    public function test_private_or_unpublished_case_study_returns_not_found(): void
    {
        $user = User::factory()->create();

        $project = Project::query()->create([
            'user_id' => $user->id,
            'name' => 'Private Project',
            'slug' => 'private-project',
            'status' => 'completed',
            'is_published' => false,
            'case_study_published' => true,
            'likes' => 0,
        ]);

        $this->get(route('projects.show', $project->slug))->assertNotFound();
    }

    public function test_contact_form_validates_and_stores_a_message(): void
    {
        Livewire::test(ContactForm::class)
            ->set('name', 'Prospective Client')
            ->set('email', 'client@example.com')
            ->set('company', 'Example Company')
            ->set('subject', 'Portfolio project discussion')
            ->set('message', 'I would like to discuss a web application with clear delivery requirements.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'client@example.com',
            'subject' => 'Portfolio project discussion',
            'status' => ContactMessage::STATUS_NEW,
        ]);
    }

    public function test_public_career_is_rendered_but_private_career_is_hidden(): void
    {
        $user = User::factory()->create();

        Career::query()->create([
            'user_id' => $user->id,
            'title' => 'Software Engineer',
            'company' => 'Public Company',
            'period' => 'Jan 2025 — Present',
            'start_date' => '2025-01-01',
            'is_current' => true,
            'description' => 'Owned delivery of production software.',
            'is_public' => true,
        ]);

        Career::query()->create([
            'user_id' => $user->id,
            'title' => 'Hidden Role',
            'company' => 'Private Company',
            'period' => '2024',
            'description' => 'This must not be public.',
            'is_public' => false,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Public Company')
            ->assertDontSee('Private Company');
    }
}
