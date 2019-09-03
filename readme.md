# LARACHAT BY SIAJI

## Getting Started
1. Clone this repo
2. Open terminal / cmd
```
composer install
```
3. Copy .env.example to .env
4. Create Laravel APP Key
```
php artisan key:generate
```
5. Run migration
```
php artisan migration
```
6. Configure Pusher Key
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=[yours]
PUSHER_APP_KEY=[yours]
PUSHER_APP_SECRET=[yours]
PUSHER_APP_CLUSTER=[yours]
```
7. Run Laravel Serve
```
php artisan serve
```