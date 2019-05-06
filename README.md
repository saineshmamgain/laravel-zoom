# Laravel-Zoom (In Development)

A package to Integrate Zoom APIs in Laravel.
##

## Installation:

    composer require saineshmamgain/laravel-zoom
    
## Generate JWT

    php artisan zoom:jwt-generate

First time command will ask for API Key, and Secret which you can get from Zoom Dashboard.

Options:

    --days, -D = No. of days for JWT to expire. Defaults to 0
    --hours, -H = No. of hours for JWT to expire. Defaults to 1
    --force, -F = Force regenerate JWT. Defaults to false

The command will update the .env file with Zoom API Key, Secret, JWT Token and Timestamp.