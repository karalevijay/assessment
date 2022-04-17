# Lumen PHP Framework

[![Build Status](https://travis-ci.org/laravel/lumen-framework.svg)](https://travis-ci.org/laravel/lumen-framework)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![Latest Stable Version](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)
[![License](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/lumen-framework)

Laravel Lumen is a stunningly fast PHP micro-framework for building web applications with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Lumen attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as routing, database abstraction, queueing, and caching.

## Official Documentation

Documentation for the framework can be found on the [Lumen website](https://lumen.laravel.com/docs).

## Contributing

Thank you for considering contributing to Lumen! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Lumen, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## How to set project
clone repo from "git@github.com:karalevijay/packt.git" url
after clone
1. composer install
    after composer install your project application will set completely on local we are ready to go
2. php -S localhost:8000 -t public
    use above command to run you project
3. run seeder to create default users (created to default user)
    php artisan db:seed --class=DatabaseSeeder

## API
1. login
2. loanStatusCheck
3. loanStatusUpdate
4. payLoanEMI
5. loanRequest
6. register -> not tested
7. logout -> not tested

## Project Architecture
    # Utility.php
        we have utilitity file for utility method like common used method written here
    # validation_message.php
        all validation messages placed here
config folder all files have some common files which are needed frequently
## Middleware
#JWTAuth
we used middleware for authonticate user session as we are working on api basis
validation done in jwtuserauth.php file on bearer token basis

##seeder
seeder created which is used to create default users 2 users created by default 