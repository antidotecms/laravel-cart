# Getting Started

## Database structure

The package will migrate the following tables for you with some amendments based on your configuration settings.

```php
php artisan vendor:publish --tag laravel-cart-migrations
```

The follwing tables will be published:

| Table             |
|-------------------|
| products          |
| customers         |
| adjustments       |
| orders            |
| order_items       |
| order_adjustments |
| order_log_items   |
| payments          |

