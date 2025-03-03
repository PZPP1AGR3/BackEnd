## Deploy

### 1. Copy environment files and set your own values if needed

```bash
cp .docker/.env.example .docker/.env
```

### 2. Build and run docker containers

1. Create docker network if doesn't exist

```bash
docker network create proxy
```

2. Build and run containers

```bash
docker compose up -d
```

### 3. Setup environment for laravel

```bash
cp .env.example .env
```

Change:

- `APP_URL` to your domain name.
- `APP_ENV` to `production` if you are deploying to production.

### 4. Install composer dependencies

```bash
docker compose exec web composer install
```

### 5. Generate application key

```bash
docker compose exec web php artisan key:generate
```

### 6. Run migrations

```bash
docker compose exec web php artisan migrate
```

### 7. Run seeders

```bash
docker compose exec web php artisan db:seed
```

This will create an admin account, `admin` with *random password* (will be displayed in the console).

---

## API Docs

#### Navigate to `${APP_URL}/docs/api` to view the API documentation.
