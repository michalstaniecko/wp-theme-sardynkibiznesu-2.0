---
name: devops
description: Use this skill when working with Docker, build processes, deployment, or environment configuration. Covers docker-compose setup, npm scripts, environment variables, and local development workflow.
---

# DevOps

## Overview

This project uses Docker for local development and Laravel Mix for asset building.

## Key Files

- `docker-compose.yml` - Docker services configuration
- `Dockerfile` - WordPress container build (if exists)
- `package.json` - NPM scripts and dependencies
- `.env` - Environment variables (not in repo)
- `webpack.mix.js` - Build configuration

## Docker Environment

### Services

```yaml
# docker-compose.yml
services:
  wordpress:
    build: .
    depends_on:
      - db
    restart: always
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: ${DB_USER:-wordpress}
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD:-wordpress}
      WORDPRESS_DB_NAME: ${DB_NAME:-wordpress}
    volumes:
      - ./wordpress:/var/www/html:rw
      - ./wp-config.php:/var/www/html/wp-config.php
      - ./wp-content/themes:/var/www/html/wp-content/themes
      - ./wp-content/uploads:/var/www/html/wp-content/uploads
      - ./wp-content/plugins:/var/www/html/wp-content/plugins:rw

  db:
    image: mysql:8.0
    restart: always
    environment:
      MYSQL_DATABASE: ${DB_NAME:-wordpress}
      MYSQL_USER: ${DB_USER:-wordpress}
      MYSQL_PASSWORD: ${DB_PASSWORD:-wordpress}
      MYSQL_RANDOM_ROOT_PASSWORD: '1'
    volumes:
      - ./database:/var/lib/mysql:rw
```

### Docker Commands

```bash
# Start environment
docker-compose up

# Start in background
docker-compose up -d

# Stop environment
docker-compose down

# View logs
docker-compose logs -f wordpress

# Rebuild containers
docker-compose build --no-cache

# Access WordPress container shell
docker-compose exec wordpress bash
```

### Environment Variables (.env)

Create `.env` file in project root:

```env
DB_USER=wordpress
DB_PASSWORD=secure_password
DB_NAME=sardynkibiznesu
```

## Build System

### NPM Scripts

```json
{
  "scripts": {
    "development": "mix watch",
    "production": "mix --production"
  }
}
```

### Development Workflow

```bash
# 1. Start Docker environment
docker-compose up -d

# 2. Install dependencies (if needed)
npm install

# 3. Start watch mode
npm run development

# 4. Make changes to src/js/ or src/scss/
# Files auto-compile on save
```

### Production Build

```bash
# Create optimized build
npm run production
```

Production build:
- Minifies JavaScript
- Minifies CSS
- Removes source maps
- Optimizes assets

## Environment Detection

### In PHP (functions.php)

```php
// Check if local environment
if (false === apply_filters('sb_is_local_env', false)) {
    // Production-only code
    include_once get_stylesheet_directory() . '/inc/tracking-codes.php';
}

// Define local environment filter
add_filter('sb_is_local_env', function() {
    return strpos($_SERVER['HTTP_HOST'], 'localhost') !== false
        || strpos($_SERVER['HTTP_HOST'], '.local') !== false;
});
```

### In JavaScript

```javascript
// Check if development
const isDevelopment = window.location.hostname === 'localhost'
    || window.location.hostname.includes('.local');

if (!isDevelopment) {
    // Production-only tracking
}
```

## Volume Mounts Explained

| Local Path | Container Path | Purpose |
|------------|---------------|---------|
| `./wordpress/` | `/var/www/html/` | WordPress core files |
| `./wp-content/themes/` | `/var/www/html/wp-content/themes/` | Theme development |
| `./wp-content/plugins/` | `/var/www/html/wp-content/plugins/` | Plugin development |
| `./wp-content/uploads/` | `/var/www/html/wp-content/uploads/` | Media files |
| `./database/` | `/var/lib/mysql/` | Database persistence |

## Best Practices

1. **Never commit .env** - Keep credentials out of version control
2. **Use volume mounts** - For live code editing without container rebuild
3. **Watch mode during dev** - Run `npm run development` while working
4. **Production build before deploy** - Always run `npm run production`
5. **Keep Docker running** - Leave `docker-compose up -d` running during development
6. **Database backups** - Periodically backup `./database/` directory
