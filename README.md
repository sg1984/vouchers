# Vouchers

This is a project of an API using Laravel Lumen to controll discount vouchers in a web shop.

The project was developed using [Laravel Lumen](https://lumen.laravel.com/), so it has to be installer using Homestead or the specification recommended to install Laravel, that is:
* PHP >= 7.0
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension

There must be a database system as well. So the vouchers can be stored somewhere, right?

There is a `.env.example` file, change it's name to `.env`, this is a standard behaviour from Laravel environment.

Clone or download the project and enter in the folder created:
```
cd /path-to/wherever-the/code-is
```  

Clone or download the project and run the composer installer:
```
composer install
```  

Now whe create the tables at the database that is defined at `.env` running the command:
```
php artisan migrate
```  

Now, if you want to know if it is all working, you can run the tests:
```
vendor/bin/phpunit
```  

This is a API, so to better test it, there is a [Postman](https://www.getpostman.com/) collection [here](https://www.getpostman.com/collections/adb0d24343835147cebd) to use the endpoints. The collection is based in a localhost server that can be started running the command:
```
php -S localhost:8000 -t public
```    

