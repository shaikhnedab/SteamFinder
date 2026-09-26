<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['he', 'ar', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#0a1120">
    <title>@yield('code') · {{ __('trans.steam_finder') }}</title>

    @if (config('app.favicon_url'))
    <link rel="icon" href="{{ config('app.favicon_url') }}"/>
    @else
    <link rel="icon" type="image/svg+xml" href="{{ asset('brand-mark.svg') }}"/>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=8">

    <script>
        (function () {
            try {
                var saved = localStorage.getItem('steamfinder-theme');
                if (!saved) {
                    saved = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
                }
                document.documentElement.setAttribute('data-theme', saved);
            } catch (e) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
</head>
<body>

    <header class="site-nav">
        <div class="nav-inner">
            <a class="brand" href="/">
                <span class="brand-mark" aria-hidden="true">
                    <img class="brand-logo" src="{{ asset('brand-mark.svg') }}" alt="" width="22" height="22">
                </span>
                {{ __('trans.steam_finder') }}
            </a>
        </div>
    </header>

    <main class="shell page-main">
        <header class="hero">
            <div class="kicker">
                <span class="mono">@yield('code')</span>
            </div>
            <h1>@yield('heading')</h1>
            <p>@yield('message')</p>
        </header>

        <div class="error-actions">
            <a class="btn btn-primary" href="/">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                {{ __('trans.back_home') }}
            </a>
        </div>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <span>&copy; SteamID Finder 2020</span>
            <span class="footer-links">
                <a href="https://github.com/shaikhnedab/SteamFinder" target="_blank" rel="noopener">GitHub</a>
            </span>
        </div>
    </footer>

</body>
</html>
