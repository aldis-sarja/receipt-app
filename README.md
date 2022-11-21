# Simple payment app
Built with PHP, nodejs, composer, Laravel 8 and Nuxt.js 2. 

## Installation

```bash
git clone https://github.com/aldis-sarja/receipt-app.git
cd receipt-app/be-laravel
composer install
```
In `be-laravel` folder rename the file `.env.example` to `.env`, or make a copy:
```bash
cp .env.example .env
```
Configure your database:
```dosini
DB_CONNECTION=<your-db-server>
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<your-db-name>
DB_USERNAME=<your-db-user-name>
DB_PASSWORD=<your-password>
```

Now initialize database and populate with sample records:
```bash
php artisan migrate
php artisan db:seed
```

```bash
php artisan key:generate
cd ../fe-nuxt
npm install
npm run build
```

## Usage
Run servers:
In `be-laravel` directory run backend server:
```bash
php artisan serve
```
In `fe-nuxt` directory run frontend server:

```bash
npm run start
```

Point your browser to address `http://localhost:3000/`