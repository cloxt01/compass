
# Compass App Documentation

`Compass` - aplikasi pendukung untuk melamar pekerjaan 


## Documentation

### Infrastructure
* **Engine:** Laravel 12+ (PHP 8.2)
* **Server:** Linux
* **Database:** MySQL
* **Web Server:** Nginx

### Installation & Setup

1. Clone the repository
```bash
git clone https://github.com/cloxt01/compass
cd compass
```

2. Install dependencies
```bash
composer install
```

4. Install Node.js dependencies & build assets
```bash
npm install
npm run build
```

3. Copy the `.env.example` file to `.env`
```bash
cp .env.example .env
```

4. Generate application key
```bash
php artisan key:generate
```
jika ingin menggunakan testing (pest)
```bash
php artisan key:generate --env=testing
```
5. Create a new MySQL database for the application
```mysql
CREATE DATABASE compass
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE DATABASE compass_testing
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```
6. Configure your database settings in the `.env` file


```dotenv
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
```

7. Run migrations and seed the database
```bash
php artisan migrate --seed
```

- jika memakai testing unit
```bash
php artisan migrate:fresh --env=testing --seed
```

8. Start the development server
```bash
php artisan serve
```

### Apply
1. Open your web browser and navigate to `http://127.0.0.1:8000`.
