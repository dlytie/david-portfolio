<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070a11">
    <meta name="color-scheme" content="dark">

    <title>{{ $portfolio['seo']['title'] }}</title>
    <meta name="description" content="{{ $portfolio['seo']['description'] }}">
    <link rel="canonical" href="{{ $portfolio['seo']['canonical'] }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $portfolio['seo']['title'] }}">
    <meta property="og:description" content="{{ $portfolio['seo']['description'] }}">
    <meta property="og:url" content="{{ $portfolio['seo']['canonical'] }}">
    <meta name="twitter:card" content="summary_large_image">
    @if ($portfolio['seo']['has_share_image'])
        <meta property="og:image" content="{{ asset($portfolio['seo']['share_image']) }}">
        <meta property="og:image:alt" content="Portofolio {{ $portfolio['profile']['name'] }} — {{ $portfolio['profile']['title'] }}">
        <meta name="twitter:image" content="{{ asset($portfolio['seo']['share_image']) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="portfolioApp"
    class="portfolio-body"
    data-page="portfolio"
>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>

    <div class="ambient ambient--one" aria-hidden="true"></div>
    <div class="ambient ambient--two" aria-hidden="true"></div>
    <div class="noise" aria-hidden="true"></div>

    <x-portfolio.navigation :portfolio="$portfolio" />

    <main id="main-content">
        <x-portfolio.hero :portfolio="$portfolio" />
        <x-portfolio.about :portfolio="$portfolio" />
        <x-portfolio.projects :projects="$portfolio['projects']" />
        <x-portfolio.contact :portfolio="$portfolio" />
    </main>

    <x-portfolio.footer :portfolio="$portfolio" />
    <x-portfolio.project-dialog :projects="$portfolio['projects']" />

    <div class="scene-tooltip" data-scene-tooltip role="status" aria-live="polite"></div>

    @php
        $sameAs = array_values(array_filter([
            $portfolio['contact']['github'],
            $portfolio['contact']['linkedin'],
        ]));

        $structuredData = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $portfolio['profile']['full_name'],
            'jobTitle' => $portfolio['profile']['title'],
            'description' => $portfolio['profile']['about'],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Medan',
                'addressRegion' => 'Sumatera Utara',
                'addressCountry' => 'ID',
            ],
            'email' => $portfolio['contact']['email'] ?: null,
            'image' => $portfolio['profile']['has_photo'] ? asset($portfolio['profile']['photo']) : null,
            'url' => $portfolio['seo']['canonical'],
            'sameAs' => $sameAs ?: null,
        ], static fn ($value) => $value !== null && $value !== '');
    @endphp
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
</body>
</html>
