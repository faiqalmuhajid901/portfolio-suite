<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Canonical production URL
    |--------------------------------------------------------------------------
    |
    | URL ini menjadi sumber kebenaran untuk canonical, sitemap,
    | Open Graph, Twitter Card, dan JSON-LD.
    |
    */

    'url' => rtrim(
        (string) env(
            'SEO_SITE_URL',
            env('APP_URL', 'http://localhost')
        ),
        '/'
    ),

    /*
    |--------------------------------------------------------------------------
    | Public identity
    |--------------------------------------------------------------------------
    */

    'site_name' => env(
        'SEO_SITE_NAME',
        'Muhammad Faiq Portfolio'
    ),

    'author' => env(
        'SEO_AUTHOR',
        'Muhammad Faiq'
    ),

    /*
    |--------------------------------------------------------------------------
    | Default metadata
    |--------------------------------------------------------------------------
    */

    'default_title' => env(
        'SEO_DEFAULT_TITLE',
        'Muhammad Faiq — Software Engineer'
    ),

    'default_description' => env(
        'SEO_DEFAULT_DESCRIPTION',
        'Portfolio of Muhammad Faiq, showcasing selected software projects, technical case studies, professional experience, and contact information.'
    ),

    'default_image' => env(
        'SEO_DEFAULT_IMAGE',
        '/images/og-default.png'
    ),

    'locale' => env(
        'SEO_LOCALE',
        'en_US'
    ),

    /*
    |--------------------------------------------------------------------------
    | Indexing control
    |--------------------------------------------------------------------------
    |
    | Local dan preview sebaiknya false.
    | Production Vercel harus true.
    |
    */

    'indexing_enabled' => filter_var(
        env('SEO_INDEXING_ENABLED', false),
        FILTER_VALIDATE_BOOL
    ),

    /*
    |--------------------------------------------------------------------------
    | Optional identity
    |--------------------------------------------------------------------------
    */

    'twitter_creator' => env(
        'SEO_TWITTER_CREATOR'
    ),

    'google_site_verification' => env(
        'GOOGLE_SITE_VERIFICATION'
    ),

];
