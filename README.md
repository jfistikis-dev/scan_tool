# Scan ME basic

## Scan ME basic

Scan ME is a Codeigniter 4 app that handles barcode scanning enabling user to perform basic functions of a point-of-sales system like : 

> - Insert products with barcode, category, prices , prudchase discount etc.
> - Sell any previsouly inserted product , by scanning the barcode and adjusting price/quantity 
> - View a list of inserted products, log of purchases/sales , export to excel

It is ideal for anyone that requires a basic POS with stock updating

## NOTICE

> - This is a local implementation so it is not secure enough for hosting on public servers.
> - It is implemented as part of a larger system but has no external dependencies nor requires internet access, to function. 

## INSTALLATION

If runnning Xampp/Wamp/Lamp just add to the htdocs and run : 
php spark migrate

> [!WARNING]
> - If running inside docker the name of mySQL container should be db or change it to whatever u like in constants.php -> DOCKER_DB_NAME. This ensures smooth operation when accessing db inside and outside the docker containers.


## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library


