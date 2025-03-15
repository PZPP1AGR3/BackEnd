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

---

## Functionalities

### Authentication

- Register (create a new user and return access token)
- Login (exchange username and password for access token)
- Fetch user details (current logged in user)

### Notes

- Create notes
- Update notes
- Delete notes
- View notes
- Search and filter notes
- Share notes

### Users

- Retrieve a list of users. (admin only)
- Create a user (admin only).
- Retrieve a user. (other than self only admin can view other users)
- Update a user. (other than self only admin can update other users)
- Delete a user. (admin only)

### Tests

- Run tests to ensure API endpoints are working as expected.

---

## Technology

### Backend

- Laravel 12
- Docker (apache with PHP 8.3)
- MySQL
- Scrabble (for API documentation)
- PHPUnit (for testing)


