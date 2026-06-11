<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Alex Rivera',
            'email' => 'alex@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->call(ProjectSeeder::class);
        $this->call(SkillSeeder::class);
        $this->call(CareerSeeder::class);
    }
}