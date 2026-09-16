# SteamFinder

Look up any Steam profile and get every ID format plus account details — SteamID, SteamID3, SteamID64, custom URL, profile links, FiveM HEX, account ID, VAC/game-ban status, profile dates and CS:GO hours.

Dark/light themes, responsive layout, click-to-copy values, multi-language UI.

## Requirements

- PHP 8.0+ with extensions: `mbstring`, `bcmath`, `gmp`, `xml`, `ctype`, `curl`, `fileinfo`, `json`, `openssl`, `tokenizer`
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

# 6. Start the local server
php artisan serve
```

Open http://127.0.0.1:8000 and enter any SteamID format, custom URL or full profile URL.

## Run with Docker

A minimal multi-stage Alpine image (~200 MB: nginx + php-fpm + supervisor, no dev tools) is built automatically on every push to `main` and published to Docker Hub as `shaikhnedab/steamfinder`. For the automated build to push, add these repository secrets on GitHub (`Settings → Secrets and variables → Actions`): `DOCKER_USERNAME` and `DOCKER_PASSWORD` (a Docker Hub access token).

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
docker pull shaikhnedab/steamfinder:latest

docker run -d \
  --name steamfinder \
  --restart unless-stopped \
  -p 8080:8000 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_LANG=en \
  -e STEAM_API_KEY=your_key_here \
  shaikhnedab/steamfinder:latest
```

Then open http://localhost:8080 (nginx serves the app on container port 80, mapped to host 8080). Check health with `docker inspect steamfinder --format '{{.State.Health.Status}}'`.

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

For production also run `php artisan config:cache` and `php artisan route:cache` after editing `.env`.

## Notes

- Never commit your `.env` file — it is git-ignored for a reason. Only `.env.example` (with empty values) belongs in the repo.
- If lookups fail with "Failed to connect", check that the server can reach `https://api.steampowered.com` and that your key is valid.

## Contributions

Feel free to contribute to this open source project.

## Support

This app was created for fun, not for production environments.
