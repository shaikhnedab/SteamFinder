# SteamFinder

Paste any Steam identifier, get every format it can become — plus profile state, VAC status, invite link and CS:GO hours. One click copies any value.

Built on Laravel 13 with a blueprint-and-copper instrument-panel UI shared with the [HVAC Design Suite](https://github.com/shaikhnedab/hvac). No database, no build step, no JavaScript framework.

## Features

- **Every ID format** from one input — SteamID, SteamID3, SteamID64, account ID, custom URL, FiveM HEX, invite link
- **Accepts anything real** — IDs in any of three formats, vanity slugs, or a pasted `steamcommunity.com` URL (query strings and fragments included)
- **Click-to-copy** on every value, with a toast and inline confirmation; clicking a row copies it too
- **Bounded upstream calls** — every Steam request has an explicit timeout, so a slow or rate-limited API can never hang a page
- **10-minute caching** of bans and playtime, failures included, so one slow lookup is never paid twice
- **Rate limiting** on both Steam-backed routes to protect your API key's quota
- **Dark and light themes**, toggled and persisted, with no flash of the wrong theme on load
- **Five languages** — English, Spanish, Russian, Hebrew (RTL), Chinese
- **Responsive** from 390 px up, keyboard-accessible, no console errors

## Requirements

- **PHP 8.3+** with `mbstring`, `gmp`, `xml`, `ctype`, `curl`, `fileinfo`, `json`, `openssl`, `tokenizer`
  (`gmp` is required by `xpaw/steamid`, which does all SteamID conversions; the Docker image is PHP 8.4)
- **Composer 2.x**
- A free [Steam Web API key](https://steamcommunity.com/dev/apikey)

No database or queue is required. The app uses file-backed sessions and cache.

Runtime dependencies are deliberately few: `laravel/framework` and
`guzzlehttp/guzzle` (its HTTP client), plus `xpaw/steamid` for ID conversion.
Steam is queried with Laravel's own HTTP client rather than a Steam SDK, so
every call has an explicit timeout. CORS is handled by the framework itself.

## Quick start

```bash
git clone https://github.com/shaikhnedab/SteamFinder.git
cd SteamFinder

composer install

cp .env.example .env
php artisan key:generate

# add your key from https://steamcommunity.com/dev/apikey
#   STEAM_API_KEY=your_key_here

# the web server user must be able to write caches, sessions and compiled views
chmod -R 775 storage bootstrap/cache

php artisan serve
```

Open <http://127.0.0.1:8000>.

## Configuration

Everything lives in `.env`. Only `STEAM_API_KEY` is required.

| Variable | Default | Purpose |
|---|---|---|
| `STEAM_API_KEY` | — | **Required.** Steam Web API key |
| `APP_LOCALE` | `en` | UI language: `en`, `es`, `ru`, `he`, `zh` (legacy name `APP_LANG` still works) |
| `APP_DEBUG` | `false` | Never enable in production — it prints stack traces |
| `APP_URL` | `http://localhost` | Canonical URL, used for generated links |
| `FAVICON_URL` | built-in `brand-mark.svg` | Browser tab icon. Full URL or a path under `public/` |
| `BRAND_LOGO_URL` | built-in `brand-mark.svg` | Navbar logo. Full URL or a path under `public/` |

Translations live in `lang/{lang}/trans.php`. All five files are kept in key parity — add a key to all of them or none.

### Custom branding

Point the env vars at a full URL, or drop a file into `public/uploads/` and point at the local path:

```bash
FAVICON_URL=/uploads/my-icon.svg
BRAND_LOGO_URL=/uploads/my-logo.png
```

Supported: `.ico`, `.png`, `.svg` for the favicon; `.png`, `.svg`, `.jpg` for the logo. Leave a variable empty to keep the default. There is deliberately no in-browser uploader — the app has no accounts, and a public upload endpoint would let anyone write files to your server.

## Input handling

Input is normalized, then classified before any network call is made:

| Shape | Example | Cost |
|---|---|---|
| SteamID64 | `76561197960287930` | no API call — converted locally |
| SteamID3 | `[U:1:231702]` | no API call |
| SteamID (v1) | `STEAM_1:0:115851` | no API call |
| Profile URL | `https://steamcommunity.com/id/valve/?xml=1` | 1 call |
| Custom URL | `gabrielnewell` | 1 call |

Anything that cannot be a Steam identifier — spaces, punctuation, non-profile URLs, absurd length — is rejected immediately without spending an API call.

The custom-URL character set is permissive on purpose: real accounts genuinely own vanity names like `0`, `-1` and `junk`, so the app defers to Steam rather than guessing. If Steam cannot resolve a slug, you get a plain error.

> The bundled `xpaw/steamid` parser matches `STEAM_` case-sensitively and rejects account IDs above 10 digits, matching Steam's own limits. Lowercase `steam_1:...` is not accepted.

## Runtime behaviour

| | Timeout | Cached |
|---|---|---|
| `ResolveVanityURL` (search) | 10 s / 5 s connect | no |
| `GetPlayerSummaries` (profile) | 6 s / 3 s connect | no |
| `GetPlayerBans` | 3 s / 2 s connect | 10 min |
| `GetOwnedGames` (CS:GO) | 4 s / 2 s connect | 10 min |

Bans and playtime are supplementary, so they are fetched with short timeouts and cached **including failures** — a timeout is paid at most once per TTL instead of on every page view. A cold profile page is three sequential calls; a warm one is served from cache.

Rate limits: **30 searches/minute** and **60 profile views/minute** per IP.

## Docker

A minimal two-stage Alpine image is built on every push to `main` and published to `ghcr.io/shaikhnedab/steamfinder`.

```bash
export STEAM_API_KEY=your_key_here      # https://steamcommunity.com/dev/apikey
docker compose up --build -d            # http://localhost:8080
```

Or without compose:

```bash
docker run -d --name steamfinder --restart unless-stopped \
  -p 8080:8000 \
  -e APP_ENV=production -e APP_DEBUG=false -e APP_LOCALE=en \
  -e STEAM_API_KEY=your_key_here \
  ghcr.io/shaikhnedab/steamfinder:latest
```

Set `APP_LOCALE` the same way for a non-English UI. Check health with
`docker inspect steamfinder --format '{{.State.Health.Status}}'`, or hit the
built-in `/up` endpoint (also available on a normal install).

The container runs as `www-data` and forks eight PHP workers, so one slow Steam call cannot block the rest of the page.

## Deployment

Point the document root at `public/`. Nothing above it is web-reachable.

**nginx**

```nginx
server {
    listen 80;
    server_name steamfinder.example.com;
    root /var/www/html/SteamFinder/public;
    index index.php;

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

**Apache** — the bundled `public/.htaccess` handles rewrites, so it only needs:

```apache
<VirtualHost *:80>
    ServerName steamfinder.example.com
    DocumentRoot /var/www/html/SteamFinder/public
    <Directory "/var/www/html/SteamFinder/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

For a container behind host nginx, proxy to `127.0.0.1:8080` and forward
`Host`, `X-Real-IP`, `X-Forwarded-For` and `X-Forwarded-Proto`.

After editing `.env` on a production host, run:

```bash
php artisan config:cache
```

`php artisan route:cache` works — the `/` route uses `Route::view()` rather than a
closure, so it can be serialized. The app is stateless, so there is nothing to
optimize beyond that.

## Troubleshooting

**"Failed to get data, please check the ID!"** — the identifier parsed but Steam
returned no player. Usually a genuinely nonexistent account. If it happens for
IDs you know exist, the profile may be private; Steam still returns a summary
for those, so this points at the key rather than privacy.

**"Failed to connect to Steam API"** — the server cannot reach
`api.steampowered.com`, or the key is invalid. Check outbound HTTPS and
`STEAM_API_KEY`.

**"Steam API key is not configured"** — `STEAM_API_KEY` is empty. Set it and run
`php artisan config:clear` (or `config:cache` on production).

**"Unrecognised ID format"** — the input cannot be a Steam identifier. See
[Input handling](#input-handling).

**Pages are slow** — profile pages make up to three sequential Steam calls when
cold (~1 s) and are near-instant when cached. If every load is slow, you are
either being rate-limited by Steam or your network cannot reach
`fonts.googleapis.com`; the font stylesheet is render-blocking, which stalls
first paint until it resolves.

**Contributing** — bug reports and pull requests are welcome. Please keep the
five translation files in sync and run `php artisan view:clear` after touching
views locally.

**Security** — never commit `.env`. It is git-ignored; only `.env.example` with
empty values belongs in the repository.
