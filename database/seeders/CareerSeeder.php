<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    public function run(): void
    {
        Career::create([
            'title' => 'Senior Portfolio Manager',
            'company' => 'Creative Systems Studio',
            'period' => '2024 - Present',
            'location' => 'Remote',
            'description' => 'Leading portfolio strategy, client-facing creative systems, and digital product delivery.',
        ]);

        Career::create([
            'title' => 'Digital Product Strategist',
            'company' => 'Monochrome Lab',
            'period' => '2021 - 2024',
            'location' => 'Jakarta',
            'description' => 'Designed workflow systems for design teams and managed cross-functional project execution.',
        ]);

        Career::create([
            'title' => 'UI/UX Designer',
            'company' => 'Arcane Digital',
            'period' => '2018 - 2021',
            'location' => 'Bandung',
            'description' => 'Built interface prototypes, design systems, and visual guidelines for multiple digital products.',
        ]);
    }
}