<!DOCTYPE html>
<html lang="{{ config('app.locale') ?? 'en' }}" dir="{{ in_array(config('app.locale'), ['he', 'ar', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0f1e">
    <title>@yield('title')</title>

    @if (config('app.favicon_url'))
    <link rel="icon" href="{{ config('app.favicon_url') }}"/>
    @else
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}"/>
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=7">
</head>
<body>

    <header class="site-nav">
        <div class="container nav-inner">
            <a class="brand" href="/">
                <span class="brand-mark" aria-hidden="true">
                    @if (config('app.brand_logo_url'))
                    <img class="brand-logo" src="{{ config('app.brand_logo_url') }}" alt="" width="30" height="30">
                    @else
                    <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z"/><path d="M8.5 14.5a2.3 2.3 0 1 0 0-4.6 2.3 2.3 0 0 0 0 4.6z"/><path d="M15.5 9a2.3 2.3 0 1 0 0-4.6 2.3 2.3 0 0 0 0 4.6z"/></svg>
                    @endif
                </span>
                {{ __('trans.steam_finder') }}
            </a>

            <button class="theme-toggle" id="themeToggle" type="button" aria-label="Toggle theme">
                <svg class="icon icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg class="icon icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>
        </div>
    </header>

    @yield('support')

    <main class="container page-main">

        @if (session('error'))
        <div class="flash flash-error" role="alert">
            <span class="flash-msg">{{ session('error') }}</span>
            <button class="flash-close" type="button" data-dismiss-flash aria-label="Dismiss">&times;</button>
        </div>
        @endif

        @if (session('status'))
        <div class="flash flash-success" role="status">
            <span class="flash-msg">{{ session('status') }}</span>
            <button class="flash-close" type="button" data-dismiss-flash aria-label="Dismiss">&times;</button>
        </div>
        @endif

        <section class="search-panel">
            <form action="/search" method="post" role="search">
                @csrf
                <label class="search-label" for="searchText">{{ __('trans.enter_steamid') }}</label>
                <div class="search-field">
                    <div class="search-input-wrap">
                        <span class="search-icon" aria-hidden="true">
                            <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input id="searchText" name="steamid" type="text" class="search-input" placeholder="{{ __('trans.search_placeholder') }}" aria-label="{{ __('trans.enter_steamid') }}" autocomplete="off" spellcheck="false" autofocus required>
                    </div>
                    <button id="search" class="btn btn-primary search-submit" type="submit">
                        <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        {{ __('trans.search_button') }}
                    </button>
                </div>
            </form>
        </section>

        @yield('content')

    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <span>© SteamID Finder 2020</span>
            <a href="http://steampowered.com/" target="_blank" rel="noopener">
                <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                Powered by Steam
            </a>
            <a href="https://github.com/shaikhnedab/SteamFinder" target="_blank" rel="noopener">
                <svg class="icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"/></svg>
                GitHub
            </a>
        </div>
    </footer>

    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>