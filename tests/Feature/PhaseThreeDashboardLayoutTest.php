<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThreeDashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_three_admin_pages_use_the_existing_dashboard_shell(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        foreach ([
            'content.index',
            'project-case-studies.index',
            'careers.index',
            'messages.index',
        ] as $routeName) {
            $response = $this->get(route($routeName));

            $response
                ->assertOk()
                ->assertSee('My')
                ->assertSee('Portfolio Suite')
                ->assertDontSee('phase-three-admin-nav');
        }
    }

    public function test_phase_three_navigation_is_integrated_into_the_sidebar(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('project-case-studies.index'))
            ->assertOk()
            ->assertSee('href="'.route('content.index').'"', false)
            ->assertSee('href="'.route('project-case-studies.index').'"', false)
            ->assertSee('href="'.route('careers.index').'"', false)
            ->assertSee('href="'.route('messages.index').'"', false)
            ->assertSee('Open Public Site');
    }
}
