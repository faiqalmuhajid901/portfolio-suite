<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table): void {
            $table->date('birth_date')->nullable();
            $table->string('domicile', 150)->nullable();
            $table->string('public_email')->nullable();

            $table->string('professional_status', 150)->nullable();
            $table->string('work_preference', 150)->nullable();

            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();

            $table->text('linkedin_url')->nullable();
            $table->text('github_url')->nullable();
            $table->text('cv_url')->nullable();

            $table->json('languages')->nullable();
            $table->json('current_focus')->nullable();

            $table->boolean('is_public')
                ->default(false)
                ->index();
        });

        /*
         * Agar profil lama langsung tetap tampil setelah migration.
         * Hanya profil pertama yang ditetapkan sebagai profil publik.
         */
        $firstProfileId = DB::table('profiles')
            ->orderBy('id')
            ->value('id');

        if ($firstProfileId !== null) {
            DB::table('profiles')
                ->where('id', $firstProfileId)
                ->update([
                    'is_public' => true,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table): void {
            $table->dropIndex(['is_public']);

            $table->dropColumn([
                'birth_date',
                'domicile',
                'public_email',
                'professional_status',
                'work_preference',
                'about_title',
                'about_description',
                'linkedin_url',
                'github_url',
                'cv_url',
                'languages',
                'current_focus',
                'is_public',
            ]);
        });
    }
};
