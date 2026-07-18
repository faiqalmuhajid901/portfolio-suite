<?php

use App\Support\PublicPortfolioCache;

beforeEach(function (): void {
    PublicPortfolioCache::forgetAll();
});

it(
    'renders simplified public navigation',
    function (): void {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(
                'href="#overview"',
                false
            )
            ->assertSee(
                'href="#about"',
                false
            )
            ->assertSee(
                'href="#portfolio"',
                false
            )
            ->assertSee(
                'href="#certificates"',
                false
            )
            ->assertSee(
                'href="#contact"',
                false
            );
    }
);

it(
    'shows the login link to public visitors',
    function (): void {
        $this->get('/')
            ->assertOk()
            ->assertSee(
                'href="' . route('login') . '"',
                false
            )
            ->assertSee('Log in');
    }
);

it(
    'uses client side project searching',
    function (): void {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(
                'x-model.debounce.150ms="projectSearch"',
                false
            )
            ->assertDontSee(
                'wire:model.live.debounce.400ms="search"',
                false
            );
    }
);

it(
    'contains reduced motion protection',
    function (): void {
        $css = file_get_contents(
            resource_path('css/app.css')
        );

        $javascript = file_get_contents(
            resource_path(
                'js/public-portfolio.js'
            )
        );

        expect($css)
            ->toContain(
                '@media (prefers-reduced-motion: reduce)'
            )
            ->toContain(
                '.hero-motion-surface'
            );

        expect($javascript)
            ->toContain(
                'requestAnimationFrame'
            )
            ->toContain(
                '(max-width: 767px)'
            )
            ->toContain(
                '(prefers-reduced-motion: reduce)'
            );
    }
);

it(
    'provides accessible public controls',
    function (): void {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee(
                'aria-label="Toggle dark mode"',
                false
            )
            ->assertSee(
                'aria-label="Toggle navigation menu"',
                false
            )
            ->assertSee(
                'aria-label="Back to top"',
                false
            );
    }
);

it(
    'shows the dashboard link to authenticated users',
    function (): void {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertOk()
            ->assertSee(
                'href="' . route('dashboard') . '"',
                false
            )
            ->assertSee('Dashboard')
            ->assertDontSee(
                'href="' . route('login') . '"',
                false
            );
    }
);
