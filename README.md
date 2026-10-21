# SteamFinder

Paste any Steam identifier, get every format it can become — plus profile state, VAC status, invite link and CS:GO hours. One click copies any value.

Built on Laravel 13 with a blueprint-and-copper instrument-panel UI. No database, no build step, no JavaScript framework.

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

# the web server user must own the writable paths: sessions, cache,
# compiled views and logs. chown is the part that matters — chmod on
# files owned by another user grants nothing to you.
sudo chown -R "$SUDO_USER:$SUDO_USER" storage bootstrap/cache
# dirs 775, files 664. Do not use `chmod -R 775`: that marks the tracked
# storage/**/.gitignore files executable and dirties `git status`.
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

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

### File ownership (do this first)

The app writes to four places at runtime, and all of them live under `storage/`
plus `bootstrap/cache/`:

| Path | Used for |
|---|---|
| `storage/framework/sessions` | session files (`SESSION_DRIVER=file`) |
| `storage/framework/cache/data` | the Steam response cache and rate-limit counters |
| `storage/framework/views` | compiled Blade templates |
| `storage/logs` | `laravel.log` |
| `bootstrap/cache` | `config:cache`, `route:cache`, package manifest |

None of them are writable unless the **PHP-FPM user** owns them. A `git clone`
run as root leaves every one of them root-owned, and PHP-FPM then fails on the
first request. Give them to the FPM user, not to your shell user:

```bash
# find the user FPM actually runs as — usually www-data
grep -E '^(user|group)' /etc/php/*/fpm/pool.d/www.conf

sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

`chmod` on its own does not fix this: on a file owned by `root:root`, mode
`775` grants write to *root's group*, and `www-data` is not in it. `chown` is
the load-bearing part.

If you would rather not have a log file at all, `LOG_CHANNEL=stderr` sends logs
to PHP's stderr, which FPM already forwards to the nginx error log. That dodges
`storage/logs` but fixes nothing else, so the `chown` is still needed.

### Web server

**nginx**

```nginx
server {
    listen 80;
    server_name steamfinder.example.com;
    root /var/www/html/SteamFinder/public;
    index index.php;

    # Keep oversized/abnormal request bodies from ever reaching PHP.
    # Mirrors post_max_size = 8M — see "PHP security" below (php2shell).
    client_max_body_size 8m;

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
    # Keep oversized/abnormal request bodies from ever reaching PHP.
    # Mirrors post_max_size = 8M — see "PHP security" below (php2shell).
    LimitRequestBody 8388608
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

## PHP security

**php2shell (CVE-2026-109618, CVE-2026-109619, disclosed 2026-10-21)** is a pair of
memory-corruption bugs in the PHP runtime itself, exploitable by an unauthenticated
HTTP request against any hosted PHP older than the patched releases — it lives in
the engine, not in application code. **Updating PHP is the only complete fix**
([php.net advisory](https://www.php.net/archive/2026.php#2026-10-21-5)); the
hardening below only trims the attack surface.

**Patched releases:** PHP 8.2.35, 8.3.36, 8.4.27 or 8.5.12. Distributions
backport the fix under the old version number (Ubuntu's is `8.5.4-0ubuntu1.5`),
so a security-pocket update carries the fix — verify your package's changelog
mentions `CVE-2026-109618`/`109619` rather than trusting `php -v`.

- **Docker image** — CI rebuilds `:latest` on every push *and* on a weekly
  schedule, and refuses to publish if the built image reports PHP below 8.4.27.
  To refresh a deployment:
  - GHCR image: `docker pull ghcr.io/shaikhnedab/steamfinder:latest`, then stop
    the old container and run a new one with the same options (see [Docker](#docker))
  - `docker compose`: `docker compose build --pull && docker compose up -d`
    (`--pull` refreshes the `php:` base image itself)
- **Bare metal (Debian/Ubuntu)** — `sudo apt update && sudo apt --only-upgrade install 'php8.*'`,
  then restart the pool: `sudo systemctl restart php8.5-fpm` (substitute your
  PHP branch; also restart Apache if using mod_php).

**Shipped hardening** (`docker/php.ini`, advisory-mitigation values):

| Setting | Why | CVE |
|---|---|---|
| `memory_limit = -1` | Removes the controllable OOM failure path; the hard boundary is the container's cgroup limit (`docker-compose.yml`) instead of a per-request cap | 109618 |
| `file_uploads = Off`, `post_max_size = 8M` | No uploads exist in this app; huge bodies are rejected before they are stored in memory | 109618 |
| `session.upload_progress.enabled = 0` | Kills the confirmed exploitation path through session upload progress | 109619 |
| `fatal_error_backtraces = 0` | Kills the confirmed fatal-backtrace path (active on PHP 8.5+, ignored by older PHP) | 109619 |
| `expose_php = Off`, `display_errors = Off` | General hygiene | — |

On bare metal use the same values, applying `memory_limit` via `php_admin_value` in
the FPM pool (or Apache config) so the application cannot override it, and enforce
the body cap at the proxy level — `client_max_body_size 8m` (nginx) /
`LimitRequestBody 8388608` (Apache) — as shown in the Web server section above.
Restart all workers after changing ini settings. Do not substitute
`RLIMIT_AS`/`RLIMIT_DATA` for the memory capacity change: per the advisory they
can themselves trigger the vulnerable allocation-failure path.

If your deployment sits behind Cloudflare, also leave the managed WAF rules
enabled: coverage against these CVEs was deployed automatically on 21 October.

## Troubleshooting

**`storage/logs/laravel.log" could not be opened in append mode: Permission
denied`** — the web server user cannot write to `storage/`, almost always
because the files are root-owned from a `git clone` run as root. See
[File ownership](#file-ownership-do-this-first).

This one is worth understanding rather than just fixing. Laravel tried to log a
problem, the log write itself failed, and the failure is an *uncaught*
`UnexpectedValueException` — so the request 500s and the message you are reading
is the logging failure, not the original fault. Fix the ownership first, then
re-read `storage/logs/laravel.log`: the real error is very likely still in there
and may be unrelated to permissions.

Note the second line, `tempnam(): file created in the system's temporary
directory`. Monolog only falls back to the system temp dir when it cannot write
in the log directory *either*, which confirms the directory itself is
unwritable rather than just the file.

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
