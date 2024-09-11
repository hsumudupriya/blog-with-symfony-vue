## About

This is a Symfony application developed by [Hasanta Sumudupriya](https://www.linkedin.com/in/hsumudupriya) as a technical task for PeopleInNeed. The objective of the application is to provide a RESTful API for a simple blog application.

The application contains following features.

1. User authentication using JWT tokens.
1. CRUD operations for blog posts.
1. Basic search functionality for blog posts.

## System requirements

This application utilizes Docker to run in development environments. Download and install Docker using this [link](https://docs.docker.com/get-started/get-docker/) to run this application easily.

Additionally you need below applications in your system to setup this project.

1. PHP 8.2 or higher with below extensions.
    1. ctype
    1. dom
    1. filter
    1. iconv
    1. json
    1. libxml
    1. mbstring
    1. openssl
    1. pcre
    1. pdo_pgsql
    1. phar
    1. reflection
    1. session
    1. simplexml
    1. sodium
    1. tokenizer
    1. xml
    1. xmlwriter
1. Composer

## Run the application.

Open bash/terminal/command line tool and run the commands below to start the application.

1. `git clone git@github.com:hsumudupriya/blog-with-symfony-vue.git`
1. `cd blog-with-symfony-vue`
1. `composer install`
1. `php bin/console lexik:jwt:generate-keypair` // The passphrase entered during the command should be the same as the value of JWT_PASSPHASE variable in the .env file. If you encounter an error running this command visit this URL for help. [https://stackoverflow.com/a/66261466/8880544](https://stackoverflow.com/a/66261466/8880544).
1. `docker-compose up -d` // Sets up the PostgreSQL database server in a docker container.
1. `symfony console doctrine:migrations:migrate`
1. `symfony console doctrine:fixtures:load` // Loads test data into the database.
1. `symfony serve -d`

Visit [https://127.0.0.1:8000/api](https://127.0.0.1:8000/api) to view the API documentation.

The application creates a test user with below credentials. You can use them to create access tokens to test the API endpoints.

Email - `test@api.com` \
Password - `abc123`

## Run the tests

Open bash/terminal/command line tool and run the command below to test the application.

1. `php bin/console --env=test doctrine:database:create` // Creates the test database.
1. `php bin/console --env=test doctrine:schema:create` // Create the tables/columns in the test database.
1. `php bin/phpunit`

Below tests are implemented in the application.

![tests](/test-results.jpg 'tests')

## Additional info

ER diagram of the application

![erd](/erd.jpg 'erd')
