<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        Project::create([
            'name' => 'Urban Flux Identity',
            'category' => 'Brand Strategy',
            'client' => 'Studio Monochrome',
            'status' => 'in_progress',
            'start_date' => '2026-10-12',
            'end_date' => '2026-11-15',
            'image' => 'images/project-1.jpg',
            'description' => 'A refined identity system for a modern urban brand.',
            'tags' => ['Branding', 'Strategy'],
            'likes' => 120,
        ]);

        Project::create([
            'name' => 'Ethereal UI Kit',
            'category' => 'Design System',
            'client' => 'Internal Tools',
            'status' => 'review',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-20',
            'image' => 'images/project-2.jpg',
            'description' => 'A soft minimalist interface kit for product teams.',
            'tags' => ['UI Kit', 'Design'],
            'likes' => 82,
        ]);

        Project::create([
            'name' => 'Fintech Dashboard',
            'category' => 'Mobile App',
            'client' => 'Arcane Bank',
            'status' => 'completed',
            'start_date' => '2026-09-15',
            'end_date' => '2026-10-30',
            'image' => 'images/project-3.jpg',
            'description' => 'Mobile-first analytics dashboard for financial users.',
            'tags' => ['Fintech', 'Analytics'],
            'likes' => 215,
        ]);

        Project::create([
            'name' => 'Sage Commerce System',
            'category' => 'E-Commerce',
            'client' => 'Sage Market',
            'status' => 'completed',
            'start_date' => '2026-08-01',
            'end_date' => '2026-09-10',
            'image' => 'images/project-4.jpg',
            'description' => 'A calm and conversion-focused commerce interface.',
            'tags' => ['Commerce', 'Web App'],
            'likes' => 164,
        ]);

        Project::create([
            'name' => 'Metric Flow Analytics',
            'category' => 'Dashboard',
            'client' => 'Metric Flow',
            'status' => 'in_progress',
            'start_date' => '2026-11-05',
            'end_date' => '2026-12-02',
            'image' => 'images/project-5.jpg',
            'description' => 'A dashboard concept for portfolio performance and user engagement.',
            'tags' => ['Analytics', 'Dashboard'],
            'likes' => 98,
        ]);

        Project::create([
            'name' => 'Lunar Brand Guidelines',
            'category' => 'Brand Manual',
            'client' => 'Lunar Space',
            'status' => 'review',
            'start_date' => '2026-10-20',
            'end_date' => '2026-11-25',
            'image' => 'images/project-6.jpg',
            'description' => 'A complete brand guideline system for visual consistency.',
            'tags' => ['Guidelines', 'Identity'],
            'likes' => 144,
        ]);

        Project::create([
            'name' => 'Aurora Mobile Kit',
            'category' => 'Mobile UI',
            'client' => 'Aurora Labs',
            'status' => 'completed',
            'start_date' => '2026-07-12',
            'end_date' => '2026-08-15',
            'image' => 'images/project-7.jpg',
            'description' => 'Reusable mobile interface components for rapid prototyping.',
            'tags' => ['Mobile', 'Component'],
            'likes' => 176,
        ]);
    }
}