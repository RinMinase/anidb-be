<h1 align="center"> Rin Minase's Anime Database<br>(Back-end API Service) </h1>

<p align="center">
    <a href="https://laravel.com">
        <img alt="Laravel" src="https://img.shields.io/badge/laravel-%5E10.0-red.svg?logo=laravel&logoColor=white&style=for-the-badge">
    </a>
    <a href="https://php.net">
        <img alt="PHP" src="https://img.shields.io/badge/php-8.2-blue.svg?logo=php&logoColor=white&style=for-the-badge">
    </a>
</p>

## Introduction
_Add info here_

## Getting Started

### Environmental variables setup

> Note: You can disable specific modules

This is done by setting these specific ENV flags to true, to disable them.

```
DISABLE_SCRAPER  - Disables WebScraper
```

1. Database (PostgreSQL)
    - These are the configuration options for the database:

        ```
        DB_CONNECTION=pgsql       <the database configuration being used by Laravel>
        DATABASE_URL=             <this is populated whenever an online database is being used>
        DB_HOST=anidb-pgsql       <docker **container name** of the database>
        DB_PORT=5432              <port used by the database>
        DB_DATABASE=anidb         <database name>
        DB_USERNAME=postgres      <database username>
        DB_PASSWORD=postgres      <database password>
        ```

        **Notes :** DB_HOST **should** use docker container name of db, by default this is 'anidb-pgsql', but yours could be different. You can check this by running `docker ps` then check the container name of the `postgres` container.

2. Web Scraper
    - Set `RELEASE_BASE_URI` to the API of the repository it fetches its list (e.g `api.github.com/repos/<UserName>/<Repository>`)

3. Cloudinary
    - Fire up your browser and login your [Cloudinary Account](https://cloudinary.com/users/login). If you have no account yet, you can [create one](https://cloudinary.com/users/register/free) for free.
    - After logging in, navigate to the [Cloudinary Console](https://cloudinary.com/console) to retrieve your Cloudinary URL
    - Copy the value of `API Environment variable` to `CLOUDINARY_URL` of your ENV file


### Running the project
1. [Download](https://www.docker.com/products/docker-desktop) and install `Docker for Windows`.

    **Note:** If you're not running Windows 10, use `Docker Toolbox` instead, [download](https://docs.docker.com/toolbox/toolbox_install_windows/#step-2-install-docker-toolbox) and install it. Also make sure that you are also running [vitualization](https://docs.docker.com/toolbox/toolbox_install_windows/#step-1-check-your-version).

2. Clone the project, then install the dependencies

    ```
    git clone https://github.com/RinMinase/anidb-be.git
    cd anidb-be
    composer install
    ```

3. Run the necessary docker containers

    ```
    docker-compose up -d
    docker-compose php sh
    ```

4. Inside the docker image, copy the env file, install the necessary dependencies and generate the API Key

    ```
    cp .env.example .env
    php artisan key:generate
    ```

5. Modify the ENV with the necessary configuration values, run the migrations
    ```
    php artisan config:clear
    php artisan migrate:fresh --seed
    ```

6. Fire up your browser and go to `localhost`.

    **Note:** If you are using `Docker Toolbox` instead of `Docker`, go to `192.168.99.100` instead.

**Note:**
If you need to access the container run, `docker-compose exec php bash`

**Note:**
In case you need to remove the images
From the project folder, run:
1. `docker-compose down`
2. `docker images`
3. Look for the IDs of `anidb`, `anidb-nginx`, `php-fpm-alpine` and `nginx`
4. Run `docker rmi <Image ID> <Image ID>...`

### Re-running the project
1. Make sure `Docker` is running, then open your terminal.

    **Note:** If you are running `Docker Toolbox`, then open the docker terminal.

2. Navigate to the project foler then run `docker-compose up -d`

3. Run the migrations when necessary, then install the dependencies also when necessary
    ```
    php artisan migrate
    composer install
    ```

4. Fire up your browser and go to `localhost`.

    **Note:** If you are using `Docker Toolbox` instead of `Docker`, go to `192.168.99.100` instead.

### Running the Swagger Generator / API Documentation Generator
1. Make sure `Docker` is running, then open your terminal.

    **Note:** If you are running `Docker Toolbox`, then open the docker terminal.

2. Navigate to the project foler then run `docker-compose up -d`

3. Run the command below:
    ```
    php artisan l5-swagger:generate
    ```
    or
    ```
    composer docs
    ```

4. Fire up your browser and go to `localhost/docs` to open Swagger UI.

### Running the Unit Tests
1. Make sure `Docker` is running, then open your terminal.

    **Note:** If you are running `Docker Toolbox`, then open the docker terminal.

2. Navigate to the project foler then run `docker-compose up -d`

3. Run the command below:
    ```
    php artisan test
    ```
    or if you want to run a specific test
    ```
    php artisan test --filter <Class Name of Test File>
    ```

### Project shorthands / aliases inside the PHP Docker container

This shortcuts were created to reduce the need to keep typing the same long commands over and over again.

| Shortcut          | Long version            |
| ----------------- | ----------------------- |
| `pa` or `artisan` | `php artisan`           |
| `la`              | `ls -la`                |
| `dump` or `da`    | `composer dumpautoload` |

### Project Structure
    .
    ├── app/                     # Application source code
    │   ├── Console/             # Important interfaces of the project
    │   ├── Controllers/         # API request receivers
    │   ├── Middleware/          # API middleware
    │   ├── Models/              # Database models
    │   ├── Providers/           # Project service providers
    │   ├── Repositories/        # Database queries
    │   ├── Requests/            # API request validators
    │   └── index.blade.php      # Landing page template
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
    ├── .env.example             # Environmental variables template
    ├── docker-compose.yml       # Main docker file
    ├── phpunit.xml              # Unit test configuration file
    ├── Procfile                 # Heroku process file
    └── ...                      # Other project files

## Built with
* <img width=20 height=20 src="https://laravel.com/img/favicon/favicon.ico"> [Laravel 10](https://laravel.com) - Core Framework
* <img width=20 height=20 src="https://www.php.net/favicon.ico"> [PHP 8.2](https://php.net) - Language syntax
* <img width=20 height=20 src="https://www.postgresql.org/favicon.ico"> [PostgreSQL](https://www.postgresql.org) - Database
* <img width=20 height=20 src="https://www.docker.com/wp-content/uploads/2022/03/vertical-logo-monochromatic-480x411.png"> [Docker](https://www.docker.com) - Container platform
* <img width=20 height=20 src="https://static1.smartbear.co/swagger/media/assets/swagger_fav.png"> [Swagger](https://swagger.io/) - API Documentation
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](https://www.heroku.com) - Hosting and Continuous Integration (CI) service
* <img width=20 height=20 src="https://phpunit.de/favicon-32x32.png"> [PHPUnit](https://phpunit.de/) - Unit Testing
* <img width=20 height=20 src="https://restfulapi.net/wp-content/uploads/rest.png"> [RESTful API](https://restfulapi.net) - HTTP Requests Architecture

## Deployed to
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](http://rin-anidb.herokuapp.com)
