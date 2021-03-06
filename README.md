# Slim PHP perso boilerplate

### This boilerplate contains:

* [Slim Framework](http://slimframework.com/)
* Full layout class
* DB class with mysql connection
* DB tables CRUD model
* Migrations from .sql files (up only)
* RESTFULL simple class
* Normalize.css
* Modernizr.js

### This boilerplate requires:

* PHP >= 5.4
* Composer.phar
* CLI access

## Configuration

To download the SLIM framework and initialize PRS0 classes autoload

    composer install

DB parameters have to be specified in TWO files (one for the migrations and one for the DB classes):

* /app/migrations/.dbup/properties.ini
* /config.php

## Migrations

The Dbup (https://github.com/brtriver/dbup) class is used to run up migrations from CLI.

Create a new SQL file named V999__xxxx.sql in /app/migrations/sql/, then run the following command :

    cd app/migrations
    php dbup.phar up

To show migrations status :

    php dbup.phar status

## Documentation

The Slim documentation is here : http://docs.slimframework.com/

The Dbup documentation is here : https://github.com/brtriver/dbup and here : http://brtriver.github.io/dbup/

The code MUST respect the PSR-0 standard and SHOULD respect the PSR-1 one : https://github.com/php-fig/fig-standards/tree/master/accepted/

## Licence

This boilerplate is released under the MIT public license, as the Slim Framework.
