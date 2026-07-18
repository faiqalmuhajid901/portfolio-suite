<?php

use App\Models\Profile;
use App\Models\Project;
use App\Models\User;

it('serves the application liveness endpoint', function (): void {
    $this->get('/up')->assertOk();
});

it('serves the database health endpoint', function (): void {
    $this->get('/health')
        ->assertOk()
        ->assertJson([
            'status' => 'ok',
            'application' => 'ok',
            'database' => 'ok',
            'cache' => 'ok',
        ]);
});

it('only exposes public profile records', function (): void {
    $privateUser = User::factory()->create();
    $publicUser = User::factory()->create();

    Profile::query()->create([
        'user_id' => $privateUser->id,
        'name' => 'Private Profile',
        'is_public' => false,
    ]);

    $publicProfile = Profile::query()->create([
        'user_id' => $publicUser->id,
        'name' => 'Public Profile',
        'is_public' => true,
    ]);

    $publishedProfileIds = Profile::query()
        ->published()
        ->orderBy('id')
        ->pluck('id')
        ->all();

    expect($publishedProfileIds)->toBe([
        $publicProfile->id,
    ]);
});

it('only exposes completed projects that are explicitly published', function (): void {
    Project::query()->create([
        'name' => 'Internal Project',
        'status' => 'in_progress',
        'is_published' => true,
        'likes' => 0,
    ]);

    Project::query()->create([
        'name' => 'Completed but Private Project',
        'status' => 'completed',
        'is_published' => false,
        'likes' => 0,
    ]);

    $publishedProject = Project::query()->create([
        'name' => 'Published Project',
        'status' => 'completed',
        'is_published' => true,
        'likes' => 0,
    ]);

    $publishedProjectIds = Project::query()
        ->published()
        ->orderBy('id')
        ->pluck('id')
        ->all();

    expect($publishedProjectIds)->toBe([
        $publishedProject->id,
    ]);
});

it('renders the public homepage', function (): void {
    $this->get('/')->assertOk();
});
