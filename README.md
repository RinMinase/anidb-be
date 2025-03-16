<h1 align="center"> Rin Minase's Anime Database<br>(Back-end API Service) </h1>

<p align="center">
    <a href="https://laravel.com">
        <img alt="Laravel" src="https://img.shields.io/badge/laravel-11-red.svg?logo=laravel&logoColor=white&style=for-the-badge">
    </a>
    <a href="https://php.net">
        <img alt="PHP" src="https://img.shields.io/badge/php-8.4-blue.svg?logo=php&logoColor=white&style=for-the-badge">
    </a>
</p>

## Introduction
_Add info here_

## Getting Started

### Environmental variables setup

1. Database

    Definition of terms:
    - **DB_HOST** - docker **container name** of the database
    - **DB_PORT** - port used by the database
    - **DB_DATABASE** - database username
    - **DB_PASSWORD** - database password

    **Notes :** DB_HOST **should** use docker container name of db, by default this is 'anidb-pgsql', but yours could be different. You can check this by running `docker ps` then check the container name of the `postgres` container.

2. Cloudinary
    - Fire up your browser and login your [Cloudinary Account](https://cloudinary.com/users/login). If you have no account yet, you can [create one](https://cloudinary.com/users/register/free) for free.
    - After logging in, navigate to the [Cloudinary Console](https://cloudinary.com/console) to retrieve your Cloudinary URL
    - Copy the value of `API Environment variable` to `CLOUDINARY_URL` of your ENV file


### Running the project

1. [Download](https://www.docker.com/products/docker-desktop) and install Docker.

2. Clone the project, then install the dependencies

    ```bash
    git clone https://github.com/RinMinase/anidb-be.git
    cd anidb-be
    ```

3. Run the necessary docker containers

    ```bash
    docker compose up -d
    docker compose exec php sh
    ```

4. Inside the docker image, copy the env file, install the necessary dependencies and generate the necessary key for laravel

    ```bash
    cp .env.example .env
    composer install
    php artisan key:generate
    ```

5. Generate the necessary API key and take note of this is as this is REQUIRED to access the API

    ```bash
    php artisan app:api-key
    ```
    or you can generate your own from any application, and add it under `API_KEY` in your `.env` file. Example:
    ```bash
    openssl rand -hex 36
    ```

6. Generate the necessary root password key and take note of this is as this is REQUIRED to create admin accounts

    ```bash
    php artisan app:generate-root-password
    ```
    or generate your own, and add it under `APP_REGISTRATION_ROOT_PASSWORD` in your `.env` file.

7. Cache the config file, then run the database migrations

    ```bash
    php artisan config:cache
    php artisan migrate:fresh --seed
    ```

7. Fire up your browser and go to `localhost`.

**Note:**
If you need to access the container run, `docker compose exec php sh`


### Running the optional containers

List of **optional** containers:

| Name   | Description                                                                |
| ------ | -------------------------------------------------------------------------- |
| dbui   | GUI for postgres using [sosedoff/pgweb](https://github.com/sosedoff/pgweb) |

You can run them individually by:

```bash
docker compose up -d <name>
```

Or run all of them by:

```bash
docker compose up -d --profile optional
```


### Re-running the project

1. Navigate inside the `php` docker container

    ```bash
    docker compose exec php sh
    ```

2. Run the migrations when necessary, then install the dependencies also when necessary

    ```bash
    php artisan migrate
    composer install
    ```

3. Fire up your browser and go to `localhost`.


### Running scheduled tasks

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to run the scheduled tasks manually

    ```bash
    php artisan schedule:run
    ```

There are a few commands specific to running tasks:

| Name              | Description                                                  |
| ----------------- | ------------------------------------------------------------ |
| `schedule:run`    | `Runs the scheduled tasks manually` **with respect to cron** |
| `schedule:work`   | `Runs the scheduler daemon / worker`                         |
| `schedule:list`   | `Lists the upcoming tasks to be run`                         |


### Running the Swagger Generator / API Documentation Generator

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to generate the documentations inside the container

    ```bash
    docs
    ```

3. Fire up your browser and go to `localhost/docs` to open Swagger UI.

### Running the Unit Tests
1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command below:
    ```bash
    php artisan test
    ```
    or if you want to run a specific test module
    ```
    php artisan test --filter <Class name of Test File | function name>
    ```
    or if you want to run a specific single test
    ```bash
    php artisan test --filter test_function_name tests/Location/of/TestCase.php
    ```

### Project shorthands / aliases inside the PHP Docker container

This shortcuts were created to reduce the need to keep typing the same long commands over and over again.

| Shortcut          | Long version            |
| ----------------- | ----------------------- |
| `pa` or `artisan` | `php artisan`           |
| `docs`            | `composer docs`         |
| `dump` or `da`    | `composer dumpautoload` |


### Project Structure
    .
    ├── app/                     # Application source code
    │   ├── docs.blade.php       # Swagger page template
    │   ├── index.blade.php      # Landing page template
    │   └── ...                  # Other application-related files
    ├── bootstrap/               # Project initializers
    │   ├── app.php              # Framework bootstrapper
    │   ├── helpers.php          # Helper functions
    │   └── routes.php           # Route definitions
    ├── config/                  # Laravel configuration files
    ├── database/                # Database migrations and seeders
    ├── docker/                  # Docker functions
    │   ├── php-config/          # PHP settings for docker
    │   ├── sites/               # Nginx sites for docker
    │   ├── nginx.dockerfile     # Nginx container docker file
    │   ├── php.dockerfile       # PHP container docker file
    │   └── ...                  # Other docker files
    ├── public/                  # Project entry point
    ├── tests/                   # Project test files
    ├── .czrc                    # Commitizen configuration file
    ├── docker-compose.yml       # Main docker file
    ├── phpunit.xml              # Unit test configuration file
    ├── Procfile                 # Heroku process file
    └── ...                      # Other project files

## Built with
* <img width=20 height=20 src="https://laravel.com/img/favicon/favicon.ico"> [Laravel 11](https://laravel.com) - Core Framework
* <img width=20 height=20 src="https://www.php.net/favicon.ico"> [PHP 8.4](https://php.net) - Language syntax
* <img width=20 height=20 src="https://www.postgresql.org/favicon.ico"> [PostgreSQL](https://www.postgresql.org) - Database
* <img width=20 height=20 src="https://www.docker.com/wp-content/uploads/2022/03/vertical-logo-monochromatic-480x411.png"> [Docker](https://www.docker.com) - Container platform
* <img width=20 height=20 src="https://sentry.io/static/favicon-46f8676a36982f8eb852ac6860387755.ico"> [Sentry](https://sentry.io/) - Application Monitoring
* <img width=20 height=20 src="https://static1.smartbear.co/swagger/media/assets/swagger_fav.png"> [Swagger](https://swagger.io/) - API Documentation
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](https://www.heroku.com) - Hosting and Continuous Integration (CI) service
* <img width=20 height=20 src="https://phpunit.de/favicon-32x32.png"> [PHPUnit](https://phpunit.de/) - Unit Testing
* <img width=20 height=20 src="https://restfulapi.net/wp-content/uploads/rest.png"> [RESTful API](https://restfulapi.net) - HTTP Requests Architecture

## Deployed to
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](http://rin-anidb.herokuapp.com)
