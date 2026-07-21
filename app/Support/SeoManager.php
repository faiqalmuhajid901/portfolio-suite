<?php

namespace App\Support;

use App\Models\Profile;
use App\Models\Project;
use Illuminate\Support\Str;

final class SeoManager
{
    /**
     * @var array{
     *     title: string,
     *     description: string,
     *     canonical: string,
     *     robots: string,
     *     type: string,
     *     image: string,
     *     image_alt: string,
     *     site_name: string,
     *     locale: string,
     *     author: string,
     *     twitter_creator: ?string,
     *     google_site_verification: ?string,
     *     json_ld: array<int, array<string, mixed>>
     * }
     */
    private array $data;

    public function __construct()
    {
        $this->reset();
    }

    public function reset(): self
    {
        $title = (string) config(
            'seo.default_title',
            'Muhammad Faiq — Software Engineer'
        );

        $description = $this->cleanDescription(
            (string) config(
                'seo.default_description',
                'Portfolio of Muhammad Faiq.'
            )
        );

        $canonical = $this->siteUrl();

        $this->data = [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $this->publicRobots(),
            'type' => 'website',
            'image' => $this->absoluteUrl(
                (string) config(
                    'seo.default_image',
                    '/images/og-default.png'
                )
            ),
            'image_alt' => $title,
            'site_name' => (string) config(
                'seo.site_name',
                'Muhammad Faiq Portfolio'
            ),
            'locale' => (string) config(
                'seo.locale',
                'en_US'
            ),
            'author' => (string) config(
                'seo.author',
                'Muhammad Faiq'
            ),
            'twitter_creator' => $this->nullableString(
                config('seo.twitter_creator')
            ),
            'google_site_verification' => $this->nullableString(
                config('seo.google_site_verification')
            ),
            'json_ld' => [],
        ];

        return $this;
    }

    public function home(?Profile $profile): self
    {
        $this->reset();

        $name = trim(
            (string) (
                $profile?->name
                ?: config('seo.author', 'Muhammad Faiq')
            )
        );

        $role = trim(
            (string) (
                $profile?->role
                ?: 'Software Engineer'
            )
        );

        $title = $name.' — '.$role;

        $description = $this->cleanDescription(
            $profile?->hero_description
            ?: $profile?->bio
            ?: config('seo.default_description')
        );

        $canonical = $this->siteUrl();

        $image = $this->absoluteUrl(
            $profile?->avatar
            ?: config('seo.default_image')
        );

        $sameAs = array_values(
            array_filter([
                $profile?->github_url,
                $profile?->linkedin_url,
            ])
        );

        $personId = $canonical.'/#person';
        $websiteId = $canonical.'/#website';
        $profilePageId = $canonical.'/#profile-page';

        $person = [
            '@type' => 'Person',
            '@id' => $personId,
            'name' => $name,
            'url' => $canonical,
            'jobTitle' => $role,
            'description' => $description,
            'image' => $image,
        ];

        if (filled($profile?->public_email)) {
            $person['email'] = $profile->public_email;
        }

        if (filled($profile?->domicile)) {
            $person['homeLocation'] = [
                '@type' => 'Place',
                'name' => $profile->domicile,
            ];
        }

        if ($sameAs !== []) {
            $person['sameAs'] = $sameAs;
        }

        $website = [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $canonical,
            'name' => (string) config(
                'seo.site_name',
                'Muhammad Faiq Portfolio'
            ),
            'description' => $description,
            'inLanguage' => 'en',
            'publisher' => [
                '@id' => $personId,
            ],
        ];

        $profilePage = [
            '@type' => 'ProfilePage',
            '@id' => $profilePageId,
            'url' => $canonical,
            'name' => $title,
            'description' => $description,
            'isPartOf' => [
                '@id' => $websiteId,
            ],
            'mainEntity' => [
                '@id' => $personId,
            ],
        ];

        if ($profile?->updated_at !== null) {
            $profilePage['dateModified'] = $profile
                ->updated_at
                ->toAtomString();
        }

        $this->data = [
            ...$this->data,
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'type' => 'profile',
            'image' => $image,
            'image_alt' => $name.' — '.$role,
            'json_ld' => [
                $website,
                $profilePage,
                $person,
            ],
        ];

        return $this;
    }

    public function project(Project $project): self
    {
        $this->reset();

        $author = (string) config(
            'seo.author',
            'Muhammad Faiq'
        );

        $title = $project->name
            .' — Case Study | '
            .$author;

        $description = $this->cleanDescription(
            $project->summary
            ?: $project->description
            ?: $project->outcome
            ?: config('seo.default_description')
        );

        $canonical = $this->siteUrl()
            .'/projects/'
            .rawurlencode((string) $project->slug);

        $image = $this->absoluteUrl(
            $project->image
            ?: config('seo.default_image')
        );

        $tags = collect($project->tags ?? [])
            ->map(
                fn (mixed $tag): string => trim(
                    (string) $tag
                )
            )
            ->filter()
            ->unique()
            ->values()
            ->all();

        $personId = $this->siteUrl().'/#person';
        $websiteId = $this->siteUrl().'/#website';
        $caseStudyId = $canonical.'/#case-study';

        $types = ['CreativeWork'];

        if (filled($project->source_code_url)) {
            $types[] = 'SoftwareSourceCode';
        }

        $creativeWork = [
            '@type' => $types,
            '@id' => $caseStudyId,
            'name' => $project->name,
            'headline' => $project->name,
            'url' => $canonical,
            'description' => $description,
            'abstract' => $project->summary
                ?: $description,
            'image' => $image,
            'inLanguage' => 'en',
            'author' => [
                '@type' => 'Person',
                '@id' => $personId,
                'name' => $author,
                'url' => $this->siteUrl(),
            ],
            'isPartOf' => [
                '@type' => 'WebSite',
                '@id' => $websiteId,
                'url' => $this->siteUrl(),
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
        ];

        if (filled($project->category)) {
            $creativeWork['genre'] = $project->category;
        }

        if ($tags !== []) {
            $creativeWork['keywords'] = implode(
                ', ',
                $tags
            );
        }

        if ($project->start_date !== null) {
            $creativeWork['dateCreated'] = $project
                ->start_date
                ->toDateString();
        }

        if ($project->updated_at !== null) {
            $creativeWork['dateModified'] = $project
                ->updated_at
                ->toAtomString();
        }

        if (filled($project->source_code_url)) {
            $creativeWork['codeRepository'] = $project
                ->source_code_url;
        }

        $sameAs = array_values(
            array_filter([
                $project->website_url,
                $project->source_code_url,
            ])
        );

        if ($sameAs !== []) {
            $creativeWork['sameAs'] = $sameAs;
        }

        $breadcrumb = [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical.'/#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Portfolio',
                    'item' => $this->siteUrl(),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $project->name,
                    'item' => $canonical,
                ],
            ],
        ];

        $this->data = [
            ...$this->data,
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'type' => 'article',
            'image' => $image,
            'image_alt' => $project->name
                .' project preview',
            'json_ld' => [
                $creativeWork,
                $breadcrumb,
            ],
        ];

        return $this;
    }

    public function noIndex(): self
    {
        $this->data['robots'] = implode(', ', [
            'noindex',
            'nofollow',
            'noarchive',
            'nosnippet',
        ]);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return $this->data;
    }

    private function siteUrl(): string
    {
        return rtrim(
            (string) config(
                'seo.url',
                config('app.url')
            ),
            '/'
        );
    }

    private function absoluteUrl(
        mixed $value
    ): string {
        $value = trim((string) $value);

        if ($value === '') {
            $value = (string) config(
                'seo.default_image',
                '/images/og-default.png'
            );
        }

        if (
            Str::startsWith(
                $value,
                ['http://', 'https://']
            )
        ) {
            return $value;
        }

        return $this->siteUrl()
            .'/'
            .ltrim($value, '/');
    }

    private function cleanDescription(
        mixed $value
    ): string {
        $value = strip_tags((string) $value);

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        ) ?: '';

        $value = trim($value);

        if ($value === '') {
            $value = (string) config(
                'seo.default_description',
                'Portfolio of Muhammad Faiq.'
            );
        }

        return Str::limit(
            $value,
            160,
            ''
        );
    }

    private function publicRobots(): string
    {
        if (
            ! (bool) config(
                'seo.indexing_enabled',
                false
            )
        ) {
            return implode(', ', [
                'noindex',
                'nofollow',
                'noarchive',
            ]);
        }

        return implode(', ', [
            'index',
            'follow',
            'max-image-preview:large',
            'max-snippet:-1',
            'max-video-preview:-1',
        ]);
    }

    private function nullableString(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : null;
    }
}
