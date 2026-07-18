<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->boolean('is_published')->default(false)->index()->after('status');
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('role', 160)->nullable()->after('description');
            $table->text('summary')->nullable()->after('role');
            $table->longText('problem')->nullable()->after('summary');
            $table->longText('solution')->nullable()->after('problem');
            $table->longText('outcome')->nullable()->after('solution');
            $table->string('source_code_url')->nullable()->after('website_url');
            $table->boolean('is_featured')->default(false)->index()->after('is_published');
            $table->boolean('case_study_published')->default(false)->index()->after('is_featured');
            $table->timestamp('case_study_published_at')->nullable()->index()->after('case_study_published');
            $table->unsignedSmallInteger('sort_order')->default(0)->index()->after('case_study_published_at');
        });

        // Preserve the previous public behaviour: completed projects remain public.
        DB::table('projects')
            ->where('status', 'completed')
            ->update(['is_published' => true]);

        $usedSlugs = [];

        DB::table('projects')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->each(function (object $project) use (&$usedSlugs): void {
                $base = Str::slug((string) $project->name) ?: 'project-'.$project->id;
                $slug = $base;
                $suffix = 2;

                while (isset($usedSlugs[$slug])) {
                    $slug = $base.'-'.$suffix;
                    $suffix++;
                }

                $usedSlugs[$slug] = true;

                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropUnique('projects_slug_unique');
            $table->dropColumn([
                'is_published',
                'slug',
                'role',
                'summary',
                'problem',
                'solution',
                'outcome',
                'source_code_url',
                'is_featured',
                'case_study_published',
                'case_study_published_at',
                'sort_order',
            ]);
        });
    }
};
