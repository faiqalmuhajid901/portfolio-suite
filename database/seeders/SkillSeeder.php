<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        Skill::create([
            'name' => 'UI/UX Strategy',
            'percentage' => 92,
        ]);

        Skill::create([
            'name' => 'Brand System',
            'percentage' => 86,
        ]);

        Skill::create([
            'name' => 'Frontend Architecture',
            'percentage' => 78,
        ]);

        Skill::create([
            'name' => 'Project Management',
            'percentage' => 95,
        ]);
    }
}