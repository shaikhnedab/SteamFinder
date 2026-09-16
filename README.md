# SteamFinder

Look up any Steam profile and get every ID format plus account details — SteamID, SteamID3, SteamID64, custom URL, profile links, FiveM HEX, account ID, VAC/game-ban status, profile dates and CS:GO hours.

Dark/light themes, responsive layout, click-to-copy values, multi-language UI.

## Requirements

- PHP 8.4+ with extensions: `mbstring`, `bcmath`, `gmp`, `xml`, `ctype`, `curl`, `fileinfo`, `json`, `openssl`, `tokenizer`
- [Composer](https://getcomposer.org/) 2.x
- A free Steam Web API key: https://steamcommunity.com/dev/apikey

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/shaikhnedab/SteamFinder.git
cd SteamFinder

# 2. Install PHP dependencies (Ubuntu/Debian example for extensions)
# sudo apt install php-mbstring php-bcmath php-gmp php-xml php-curl
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Add your Steam API key to .env
# STEAM_API_KEY=your_key_here   (get one at https://steamcommunity.com/dev/apikey)

# 5. Fix storage permissions (Linux)
chmod -R 775 storage bootstrap/cache

# 6. Start the local server (development only — for real hosting point
#    Apache/nginx at the public/ directory, see Deployment below)
php artisan serve
```

Open http://127.0.0.1:8000 and enter any SteamID format, custom URL or full profile URL.

## Run with Docker

A minimal multi-stage Alpine image (~150 MB, single `artisan serve` process) is built automatically on every push to `main` and published to GitHub Container Registry as `ghcr.io/shaikhnedab/steamfinder`. It authenticates with the `GHCR_PAT` repository secret (`Settings → Secrets and variables → Actions`), already configured.

### With docker compose (recommended)

```bash
# 1. Export your Steam API key (get one at https://steamcommunity.com/dev/apikey)
export STEAM_API_KEY=your_key_here

# 2. Build and start (app will be at http://localhost:8080)
docker compose up --build -d

# 3. Follow logs / stop
docker compose logs -f
docker compose down
```

`APP_LANG` can be set the same way (`APP_LANG=ru docker compose up --build -d`).

### With docker run

```bash
# Pull the prebuilt image (or build locally: docker build -t steamfinder .)
# If the package is private: docker login ghcr.io  (or set the package to public)
docker pull ghcr.io/shaikhnedab/steamfinder:latest

docker run -d \
  --name steamfinder \
  --restart unless-stopped \
  -p 8080:8000 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_LANG=en \
  -e STEAM_API_KEY=your_key_here \
  ghcr.io/shaikhnedab/steamfinder:latest
```

Then open http://localhost:8080. Check health with `docker inspect steamfinder --format '{{.State.Health.Status}}'`.

## Accepted input formats

- SteamID — e.g. `STEAM_1:0:84901`
- SteamID3 — e.g. `[U:1:169802]`
- SteamID64 — e.g. `76561197960435530`
- Custom URL — e.g. `robinwalker`
- Complete URL — e.g. `https://steamcommunity.com/id/robinwalker/`

## Configuration

| Variable         | Description                                              |
|------------------|----------------------------------------------------------|
| `STEAM_API_KEY`  | Steam Web API key (required for lookups)                 |
| `APP_LANG`       | UI language: `en`, `es`, `ru`, `he`, `zh` (default `en`) |

Translation files live in `resources/lang/{lang}/trans.php`.

## Deployment (Apache example)

```bash
sudo nano /etc/apache2/sites-available/steamfinder.conf
```

```apache
<VirtualHost *:80>
    ServerName steamfinder.example.com
    DocumentRoot /var/www/html/SteamFinder/public

    <Directory "/var/www/html/SteamFinder/public">
        AllowOverride All
    </Directory>
</VirtualHost>
```

```bash
sudo a2ensite steamfinder.conf
sudo systemctl restart apache2
```

## Deployment (nginx example, normal hosting)

Point the document root at `public/` and pass PHP files to php-fpm:

```nginx
server {
    listen 80;
    server_name steamfinder.example.com;
    root /var/www/html/SteamFinder/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.4-fpm.sock;  # or 127.0.0.1:9000
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/steamfinder /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## Reverse proxy (nginx in front of Docker)

If the app runs in Docker (`docker compose up`, host port 8080), put host nginx in front for TLS/canonical hostnames:

```nginx
server {
    listen 80;
    server_name steamfinder.example.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

For production also run `php artisan config:cache` after editing `.env`. (Do **not** run `route:cache` — the `/` route is a closure, which Laravel cannot cache.)

## Notes

- Never commit your `.env` file — it is git-ignored for a reason. Only `.env.example` (with empty values) belongs in the repo.
- If lookups fail with "Failed to connect", check that the server can reach `https://api.steampowered.com` and that your key is valid.

## Contributions

Feel free to contribute to this open source project.

## Support

This app was created for fun, not for production environments.
