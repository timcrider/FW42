Framework 42 (FW42) is a conceptual learning framework.

The goal of this framework is to be an educational framework. This may become the base for a bigger project in the future.

## Requirements

PHP 5.3+
Zend_Framework 1.11.11
PHPUnit 3.6.12

## Setup

### Create a database ( and database credentials if needed ). 
A shell command has been provided to assist in this as long as you have root access.
```shell
$ ./bin/create-dbuser.sh 
Usage: ./bin/create-dbuser.sh dbname dbuser dbpass
```

### Copy the default configuration, and modify the database credentials
```shell
$ cp config/configuration.default.php config/configuration.php
$ $EDITOR config/configuration.php
```

### Run the working script and see if you get any errors
```shell
$ php bin/working.php 
FW42 is working properly
```

### Setup optional database tables.
You can import 2 testing tables (Test and Testing) using the sql in: support/data/test-tables.sql

## Testing
You can test the repository using PHPUnit
```shell
$ phpunit
```
