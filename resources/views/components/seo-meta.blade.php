<title>{{ $seo['title'] }}</title>

<meta
    name="description"
    content="{{ $seo['description'] }}"
>

<meta
    name="author"
    content="{{ $seo['author'] }}"
>

<meta
    name="robots"
    content="{{ $seo['robots'] }}"
>

<meta
    name="googlebot"
    content="{{ $seo['robots'] }}"
>

<link
    rel="canonical"
    href="{{ $seo['canonical'] }}"
>

<meta
    property="og:site_name"
    content="{{ $seo['site_name'] }}"
>

<meta
    property="og:title"
    content="{{ $seo['title'] }}"
>

<meta
    property="og:description"
    content="{{ $seo['description'] }}"
>

<meta
    property="og:type"
    content="{{ $seo['type'] }}"
>

<meta
    property="og:url"
    content="{{ $seo['canonical'] }}"
>

<meta
    property="og:image"
    content="{{ $seo['image'] }}"
>

<meta
    property="og:image:secure_url"
    content="{{ $seo['image'] }}"
>

<meta
    property="og:image:alt"
    content="{{ $seo['image_alt'] }}"
>

<meta
    property="og:locale"
    content="{{ $seo['locale'] }}"
>

<meta
    name="twitter:card"
    content="summary_large_image"
>

<meta
    name="twitter:title"
    content="{{ $seo['title'] }}"
>

<meta
    name="twitter:description"
    content="{{ $seo['description'] }}"
>

<meta
    name="twitter:image"
    content="{{ $seo['image'] }}"
>

<meta
    name="twitter:image:alt"
    content="{{ $seo['image_alt'] }}"
>

@if (filled($seo['twitter_creator']))
    <meta
        name="twitter:creator"
        content="{{ '@'.ltrim($seo['twitter_creator'], '@') }}"
    >
@endif

@if (filled($seo['google_site_verification']))
    <meta
        name="google-site-verification"
        content="{{ $seo['google_site_verification'] }}"
    >
@endif

<meta
    name="theme-color"
    content="#0f172a"
>

@if (! empty($seo['json_ld']))
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => $seo['json_ld'],
        ];
    @endphp

    <script type="application/ld+json">{!! json_encode(
        $jsonLd,
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_HEX_AMP
        | JSON_HEX_APOS
        | JSON_HEX_QUOT
    ) !!}</script>
@endif
