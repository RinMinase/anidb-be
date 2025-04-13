<h1 align="center"> Rin Minase's Anime Database<br>(Back-end API Service) </h1>

<p align="center">
    <a href="https://laravel.com">
        <img alt="Laravel" src="https://img.shields.io/badge/laravel-12-red.svg?logo=laravel&logoColor=white&style=for-the-badge">
    </a>
    <a href="https://php.net">
        <img alt="PHP" src="https://img.shields.io/badge/php-8.4-blue.svg?logo=php&logoColor=white&style=for-the-badge">
    </a>
</p>

## Introduction
_Add info here_

## Table Of Contents

- [Getting Started](#getting-started)
    - [Environment variables setup](#environment-variables-setup)
    - [Running the project](#running-the-project)
    - [Job / Commands / Schedule updates](#job--commands--schedule-updates-and-restarting-the-supervisor)
    - [Managing the supervisor](#managing-the-supervisor)
    - [Running the optional containers](#running-the-optional-containers)
    - [Re-running the project](#re-running-the-project)
    - [Running scheduled tasks](#running-scheduled-tasks)
    - [Running the Swagger Generator / API Documentation Generator](#running-the-swagger-generator--api-documentation-generator)
    - [Running the Unit Tests](#running-the-unit-tests)
    - [Project shorthands / aliases inside the PHP Docker container](#project-shorthands--aliases-inside-the-php-docker-container)
- [Project Structure](#project-structure)
- [Project Tech Stack](#built-with)

## Getting Started

### Environment variables setup
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

1. Database

    Definition of terms:
    - **DB_HOST** - docker **container name** of the database
    - **DB_PORT** - port used by the database
    - **DB_DATABASE** - database name
    - **DB_USERNAME** - database username
    - **DB_PASSWORD** - database password

    **Notes :** DB_HOST **should** use docker container name of db, by default this is 'anidb-pgsql', but yours could be different. You can check this by running `docker ps` then check the container name of the `postgres` container.

2. Cloudinary
    - Fire up your browser and login your [Cloudinary Account](https://cloudinary.com/users/login). If you have no account yet, you can [create one](https://cloudinary.com/users/register/free) for free.
    - After logging in, navigate to the [Cloudinary Console](https://cloudinary.com/console) to retrieve your Cloudinary URL
    - Copy the value of `API Environment variable` to `CLOUDINARY_URL` of your ENV file


### Running the project
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

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

    This runs only the necessary containers. As for running the optional containers, please see the section [below](#running-the-optional-containers).

4. Inside the docker image, copy the env file, install the necessary dependencies and generate the necessary key for laravel

    ```bash
    cp .env.example .env
    composer install
    php artisan key:generate
    ```

5. Generate the necessary API key and take note of this is as this is REQUIRED to access the API

    ```bash
    php artisan app:generate-api-key
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

7. Run the database migrations

    ```bash
    php artisan migrate:fresh --seed
    ```

8. Lastly, fire up your browser and go to `localhost`.

**Note:**
If you need to access the container run, `docker compose exec php sh`


### Job / Commands / Schedule updates and restarting the supervisor
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

In cases there are any updates to:
- Jobs (found on `app/Jobs`)
- Commands (found on `app/Commands`)
- Schedules (found on `bootstrap/app.php` under `withSchedule`)

Please run the following: 

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to restart the <worker> group (`queue-worker` and `schedule-worker`)

    ```bash
    supervisorctl restart worker:
    ```


### Managing the supervisor
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

This application runs supervisor on the `php` container. Supervisor runs these tasks:

| Task Name       | Group   | Command                     | Description                    |
| --------------- | ------- | --------------------------- | ------------------------------ |
| php-fpm         | -       | `php-fpm`                   | Runs FastCGI Process Manager   |
| queue-worker    | worker  | `php artisan queue:work`    | Runs Laravel's Queue worker    |
| schedule-worker | worker  | `php artisan schedule:work` | Runs Laravel's Schedule worker |

To manage the supervisor the commands below can be used:

| Command                           | Description                                    |
| --------------------------------- | ---------------------------------------------- |
| supervisorctl reread              | Re-reads changes in supervisor config files    |
| supervisorctl update              | Updates supervisor with changes after `reread` |
| supervisorctl status              | Check status of all running tasks              |
| supervisorctl start <task name>   | Starts the task                                |
| supervisorctl stop <task name>    | Stops the task                                 |
| supervisorctl restart <task name> | Restarts the task                              |

**Please note:** Supervisor logs are kept in `./docker/logs/supervisor.log`


### Running the optional containers
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

List of **optional** containers:

| Name   | Description    |
| ------ | -------------- |
| (none) | None as of yet |

You can run them individually by:

```bash
docker compose up -d <name>
```

Or run all of them by:

```bash
docker compose up -d --profile optional
```


### Re-running the project
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

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


### Running the queue manually
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to run the worker for the queue

    ```bash
    php artisan queue:work
    ```


### Running the scheduled tasks manually
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to run the scheduled tasks manually

    ```bash
    php artisan schedule:work
    ```

There are a few commands specific to running tasks:

| Name              | Description                                                  |
| ----------------- | ------------------------------------------------------------ |
| `schedule:run`    | `Runs the scheduled tasks manually` **with respect to cron** |
| `schedule:work`   | `Runs the scheduler worker`                                  |
| `schedule:list`   | `Lists the upcoming tasks to be run`                         |


### Running the Swagger Generator / API Documentation Generator
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

1. Navigate inside the `php` docker container [[how]](#re-running-the-project)

2. Run the command to generate the documentations inside the container

    ```bash
    docs
    ```

3. Fire up your browser and go to `localhost/docs` to open Swagger UI.


### Running the Unit Tests
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

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
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

This shortcuts were created to reduce the need to keep typing the same long commands over and over again.

| Shortcut          | Long version            |
| ----------------- | ----------------------- |
| `pa` or `artisan` | `php artisan`           |
| `docs`            | `composer docs`         |
| `dump` or `da`    | `composer dumpautoload` |
| `sv`              | `supervisorctl` |


## Project Structure
<sub><sup>[Return to the table of contents](#table-of-contents)</sup></sub>

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
    │   ├── caddyfile            # Caddy container docker file
    │   ├── php.dockerfile       # PHP container docker file
    │   └── ...                  # Other docker-related files
    ├── public/                  # Project entry point
    ├── tests/                   # Project test files
    ├── .czrc                    # Commitizen configuration file
    ├── docker-compose.yml       # Main docker file
    ├── phpunit.xml              # Unit test configuration file
    ├── Procfile                 # Heroku process file
    └── ...                      # Other project files


## Built with
* <img width=20 height=20 src="https://laravel.com/img/favicon/favicon.ico"> [Laravel 12](https://laravel.com) - Core Framework
* <img width=20 height=20 src="https://www.php.net/favicon.ico"> [PHP 8.4](https://php.net) - Language syntax
* <img width=20 height=20 src="https://www.postgresql.org/favicon.ico"> [PostgreSQL](https://www.postgresql.org) - Database
* <img width=20 height=20 src="https://caddyserver.com/resources/images/favicon.png"> [Caddy](https://caddyserver.com/) - Local HTTP Server
* <img width=20 height=20 src="https://www.docker.com/wp-content/uploads/2022/03/vertical-logo-monochromatic-480x411.png"> [Docker](https://www.docker.com) - Container platform
* <img width=20 height=20 src="https://sentry.io/static/favicon-46f8676a36982f8eb852ac6860387755.ico"> [Sentry](https://sentry.io/) - Application Monitoring
* <img width=20 height=20 src="https://static1.smartbear.co/swagger/media/assets/swagger_fav.png"> [Swagger](https://swagger.io/) - API Documentation
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](https://www.heroku.com) - Hosting and Continuous Integration (CI) service
* <img width=20 height=20 src="https://phpunit.de/favicon-32x32.png"> [PHPUnit](https://phpunit.de/) - Unit Testing

## Deployed to
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](http://rin-anidb.herokuapp.com)
