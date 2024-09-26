# language_translate
laravel add language translate projects


```bash
cd storage/
mkdir -p framework/{sessions,views,cache}
chmod -R 775 framework
```
3. Run following commands

```bash
composer install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```


```bash
php artisan migrate
```

```bash
php artisan db:seed
```

### Usage

To import your translations, run the following command:

```bash
php artisan translations:import
composer require stichoza/google-translate-php
```

### Serve Project

```bash
php artisan serve
```