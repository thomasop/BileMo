BileMo

# Openclassrooms-P7-BileMo

Welcome in your API REST.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c1b5a53c1f76435cbf8ba59cdbcb237b)](https://www.codacy.com/gh/thomasop/BileMo/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=thomasop/BileMo&amp;utm_campaign=Badge_Grade)

[![Maintainability](https://api.codeclimate.com/v1/badges/cf0d5acdb153211eb532/maintainability)](https://codeclimate.com/github/thomasop/BileMo/maintainability)

## How to install the project

### Prerequisite
PHP 7.2.5 or higher

Download Wamp, Xampp, Mamp or WebHost

Symfony 4.4

Composer

### Clone
Go in directory.
Make a clone with git clone https://github.com/thomasop/BileMo.git

### Configuration
Update environnements variables in the .env file with your values.
At the very least you need to define the SYMFONY_ENV=prod
DATABASE_URL

### Composer
Install composer with composer install and init the projet with composer init in BileMo

### Database creation
*  Use the command php bin/console doctrine:database:create for database creation.
*  Use the command php bin/console doctrine:migrations:migrate for creation of the tables.
*  Use the command php bin/console doctrine:fixtures:load for load some data in database

### Start the project
At the root of your project use the command php bin/console server:start -d
Default connection use Username :mail@gmail.com - Password :Test1234?
