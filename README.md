
# Compass App Documentation

`Compass` - aplikasi pendukung untuk melamar pekerjaan 

## Installation

1. Clone the repository:
	```bash
	git clone https://github.com/cloxt01/compass.git
	```
2. Install dependencies:
	```bash
	composer install
	npm install && npm run dev
	```
3. Copy the example environment file and set your configuration:
	```bash
	cp .env.example .env
	php artisan key:generate
	```
4. Set up your database in the `.env` file and run migrations:
	```bash
	php artisan migrate
	```
5. Start the development server:
	```bash
	php artisan serve
	```


## Documentation

### Tech Stack & Infrastructure
* **Engine:** Laravel 12+ (PHP 8.2)
* **Server:** Railway
* **Database:** Redis + MySQL
* **Process Manager:** Laravel Queues + BullMQ
* **Worker:** Node Worker + Laravel Worker 

### Production Env
<img width="1917" height="911" alt="Screenshot 2026-02-14 005907" src="https://github.com/user-attachments/assets/3a08425d-2c75-41ee-bae8-498d5d0e900c" />
