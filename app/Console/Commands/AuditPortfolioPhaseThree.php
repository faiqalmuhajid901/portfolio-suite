<?php

namespace App\Console\Commands;

use App\Models\Career;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class AuditPortfolioPhaseThree extends Command
{
    protected $signature = 'portfolio:audit-phase3';

    protected $description = 'Audit whether the portfolio satisfies the Phase 3 professional-content acceptance criteria.';

    public function handle(): int
    {
        $checks = [];

        $checks[] = $this->check(
            'Project schema',
            Schema::hasColumns('projects', [
                'slug',
                'is_published',
                'role',
                'summary',
                'problem',
                'solution',
                'outcome',
                'source_code_url',
                'case_study_published',
            ]),
            'Run the Phase 3 migrations.'
        );

        $checks[] = $this->check(
            'Career schema',
            Schema::hasColumns('careers', [
                'user_id',
                'start_date',
                'end_date',
                'achievements',
                'technologies',
                'is_public',
            ]),
            'Run the career upgrade migration.'
        );

        $checks[] = $this->check(
            'Contact inbox schema',
            Schema::hasTable('contact_messages'),
            'Run the contact_messages migration.'
        );

        $checks[] = $this->check(
            'Required routes',
            collect([
                'home',
                'projects.show',
                'content.index',
                'project-case-studies.index',
                'careers.index',
                'messages.index',
                'health',
            ])->every(fn (string $name): bool => Route::has($name)),
            'Replace routes/web.php with the supplied version.'
        );

        $checks[] = $this->check(
            'Heartbeat removed',
            ! Route::has('analytics.heartbeat'),
            'Remove the obsolete analytics heartbeat route.'
        );

        $publishedProfile = Schema::hasTable('profiles')
            ? Profile::query()->published()->first()
            : null;

        $checks[] = $this->check(
            'Public profile',
            $publishedProfile !== null,
            'Publish one profile by setting is_public = true.'
        );

        if ($publishedProfile) {
            $checks[] = $this->check(
                'Hero positioning',
                filled($publishedProfile->role)
                    && filled($publishedProfile->hero_title)
                    && filled($publishedProfile->hero_description),
                'Complete role, hero_title, and hero_description.'
            );

            $checks[] = $this->check(
                'Professional contact identity',
                filled($publishedProfile->public_email)
                    && filled($publishedProfile->github_url),
                'Complete public_email and github_url.'
            );
        }

        $publicProjects = Schema::hasTable('projects')
            && Schema::hasColumn('projects', 'is_published')
            ? Project::query()->published()->get()
            : collect();

        $caseStudies = Schema::hasTable('projects')
            && Schema::hasColumn('projects', 'case_study_published')
            ? Project::query()->caseStudyPublished()->get()
            : collect();

        $checks[] = $this->check(
            'Public projects',
            $publicProjects->count() >= 3,
            'Publish at least three completed projects.'
        );

        $checks[] = $this->check(
            'Published case studies',
            $caseStudies->count() >= 3,
            'Publish at least three complete case studies.'
        );

        $incompleteCaseStudies = $caseStudies->filter(function (Project $project): bool {
            return collect([
                $project->slug,
                $project->role,
                $project->summary,
                $project->problem,
                $project->solution,
                $project->outcome,
            ])->contains(fn ($value): bool => blank($value));
        });

        $checks[] = $this->check(
            'Case-study substance',
            $incompleteCaseStudies->isEmpty(),
            $incompleteCaseStudies->isEmpty()
                ? ''
                : 'Incomplete: '.$incompleteCaseStudies->pluck('name')->implode(', ')
        );

        $checks[] = $this->check(
            'Source-code evidence',
            $publicProjects->contains(fn (Project $project): bool => filled($project->source_code_url)),
            'Add at least one permitted source-code URL.'
        );

        $publicCareers = Schema::hasTable('careers')
            && Schema::hasColumn('careers', 'is_public')
            ? Career::query()->public()->get()
            : collect();

        $checks[] = $this->check(
            'Career timeline',
            $publicCareers->isNotEmpty(),
            'Publish at least one substantive career entry.'
        );

        $checks[] = $this->check(
            'Career evidence',
            $publicCareers->isEmpty()
                ? false
                : $publicCareers->every(fn (Career $career): bool => filled($career->description)
                    && filled($career->achievements)),
            'Each public career entry needs a responsibility summary and at least one achievement.'
        );

        $rows = collect($checks)->map(fn (array $check): array => [
            $check['passed'] ? 'PASS' : 'FAIL',
            $check['name'],
            $check['note'],
        ])->all();

        $this->table(['Result', 'Check', 'Required action'], $rows);

        $failed = collect($checks)->where('passed', false);

        if ($failed->isNotEmpty()) {
            $this->error('Phase 3 is not complete. '.$failed->count().' acceptance check(s) failed.');
            return self::FAILURE;
        }

        $this->info('Phase 3 acceptance audit passed.');
        return self::SUCCESS;
    }

    private function check(string $name, bool $passed, string $note): array
    {
        return compact('name', 'passed', 'note');
    }
}
