# Dishtansya

Dishtansya is a food delivery app that provides delivery service from food chains and
restaurants around the globe

## Installation

Clone the repository

```shell
git clone https://github.com/avidianity/dishtansya.git
```

Install dependencies

```shell
composer install
```

Migrate database, seed products and setup queue (default queue driver is 'database')

```shell
php artisan migrate -seed
```

Note: After setup, JWT token must be generated after.

```shell
php artisan jwt:secret
```

Test API endpoints

```shell
php artisan test
```
