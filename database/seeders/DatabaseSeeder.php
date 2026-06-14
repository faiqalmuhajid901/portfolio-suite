<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'alex@example.com'],
            [
                'name' => 'Alex Rivera',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            // ProjectSeeder::class,
            // SkillSeeder::class,
            // CertificateSeeder::class,
        ]);
    }
}