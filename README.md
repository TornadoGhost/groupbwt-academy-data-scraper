<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Instructions for Running this Project

Before you start, you must have installed Composer and Docker.
Steps to start the project:

- Copy `.env.example` file and change name to `.env`
- composer install
- run docker
- ./vendor/bin/sail up -d
- ./vendor/bin/sail npm install
- ./vendor/bin/sail artisan key:generate
- ./vendor/bin/sail artisan migrate
- ./vendor/bin/sail artisan db:seed
- ./vendor/bin/sail artisan passport:client --personal, after you will get `Client ID` and `Client secret`, inside of
  `.env` you will find PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
  PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="unhashed-client-secret-value" and put data accordingly
- ./vendor/bin/sail artisan storage:link
- ./vendor/bin/sail composer require maatwebsite/excel

## Additional

For removing revoked users tokens you can do it manually or with schedule.
For manually run `./vendor/bin/sail artisan passport:purge` to remove all current revoked tokens.
If you don't want to purge tokens manually every day, you can use schedule, run `./vendor/bin/sail artisan shedule:list`.
You can config scheduler for how often it will be removing revoked tokens, you just need find this file `routes/console.php` and
change method. [(hourly(), daily(), weekly()...)](https://laravel.com/docs/11.x/scheduling#schedule-frequency-options) and so on
should run.
## Status codes for Scraping session

`RUNNING = 0`
`COMPLETED = 1`
`FAILED = 13`

## DataBase documentation

[ER-Diagram](https://dbdiagram.io/d/Scraping-Data-v-last-670a88bd97a66db9a3c012a4)

## API documentation

[API-Swagger](https://app.swaggerhub.com/apis/TORNADOGHOST/Scraper/1.0.0)
