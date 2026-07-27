<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ $metaTitle ?? 'BPPU | IKIP Siliwangi' }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Meta Tags -->
        <meta name="description" content="{{ $metaDescription ?? 'Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi - Mengelola dan mengembangkan usaha untuk kemajuan institusi' }}">
        <meta name="keywords" content="{{ $metaKeywords ?? 'BPPU, IKIP Siliwangi, Pengelola Usaha, Pengembangan Usaha' }}">
        <meta name="author" content="{{ $metaAuthor ?? 'BPPU IKIP Siliwangi' }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if(isset($canonicalUrl))
        <link rel="canonical" href="{{ $canonicalUrl }}">
        @endif

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:url" content="{{ $ogUrl ?? url()->current() }}">
        <meta property="og:title" content="{{ $ogTitle ?? 'BPPU IKIP Siliwangi' }}">
        <meta property="og:description" content="{{ $ogDescription ?? 'Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi - Mengelola dan mengembangkan usaha untuk kemajuan institusi' }}">
        <meta property="og:image" content="{{ $ogImage ?? asset('storage/logo-round_ijokuning.png') }}">
        <meta property="og:image:secure_url" content="{{ $ogImage ?? asset('storage/logo-round_ijokuning.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $ogTitle ?? 'BPPU IKIP Siliwangi' }}">
        <meta property="og:site_name" content="{{ $ogSiteName ?? 'BPPU IKIP Siliwangi' }}">
        <meta property="og:locale" content="{{ $ogLocale ?? 'id_ID' }}">

        @if(isset($articlePublishedTime))
        <meta property="article:published_time" content="{{ $articlePublishedTime }}">
        @endif
        @if(isset($articleModifiedTime))
        <meta property="article:modified_time" content="{{ $articleModifiedTime }}">
        @endif
        @if(isset($articleAuthor))
        <meta property="article:author" content="{{ $articleAuthor }}">
        @endif
        @if(isset($articleSection))
        <meta property="article:section" content="{{ $articleSection }}">
        @endif

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ $ogUrl ?? url()->current() }}">
        <meta name="twitter:title" content="{{ $ogTitle ?? 'BPPU IKIP Siliwangi' }}">
        <meta name="twitter:description" content="{{ $ogDescription ?? 'Badan Pengelola dan Pengembangan Usaha IKIP Siliwangi - Mengelola dan mengembangkan usaha untuk kemajuan institusi' }}">
        <meta name="twitter:image" content="{{ $ogImage ?? asset('storage/logo-round_ijokuning.png') }}">

        <!-- Scripts -->
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
