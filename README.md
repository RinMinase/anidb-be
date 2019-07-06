<h1 align="center"> Rin Minase's Anime Database<br>(Back-end API Service) </h1>

<p align="center">
    <a href="">
        <img alt="Circle-CI" src="https://rin-heroku-badge.herokuapp.com/?app=rin-anidb&svg=1&root=deploy-status">
    </a>
    <a href="http://isitmaintained.com/project/RinMinase/anidb-be">
        <img alt="Issue Resolution" src="http://isitmaintained.com/badge/resolution/RinMinase/anidb-be.svg">
    </a>
    <a href="http://isitmaintained.com/project/RinMinase/anidb-be">
        <img alt="Open Issues" src="http://isitmaintained.com/badge/open/RinMinase/anidb-be.svg">
    </a>
</p>
<p align="center">
    <a href="https://lumen.laravel.com/">
        <img alt="Lumen" src="https://img.shields.io/badge/lumen-%5E5.8.8-red.svg?logo=laravel&logoColor=white">
    </a>
    <a href="https://php.net/">
        <img alt="PHP" src="https://img.shields.io/badge/php-7.2.19-blue.svg?logo=php&logoColor=white">
    </a>
</p>

## Introduction
_Add info here_

## Getting Started

### Running the project
1. If you are running Windows 10, [download](https://download.docker.com/win/stable/Docker%20for%20Windows%20Installer.exe) and install `Docker for Windows`.

    **Note:** If you're not running Windows 10, use `Docker Toolbox` instead, [download](https://docs.docker.com/toolbox/toolbox_install_windows/) and install it. Also make sure that you are also running `vitualization`.

2. Clone the project

    ```
    git clone https://github.com/RinMinase/anidb-be.git
    cd anidb-be
    ```

3. Run the necessary docker containers

    ```
    docker-compose up -d
    docker exec -it anidb bash
    ```

4. Inside the docker image, run install dependencies and generate application key

    ```
    cp .env.example .env
    composer install
    php artisan key:generate
    ```

5. Fire up your browser and go to `localhost`.

    **Note:** If you are using `Docker Toolbox` instead of `Docker`, go to `192.168.99.100` instead.

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

3. Fire up your browser and go to `localhost`.

    **Note:** If you are using `Docker Toolbox` instead of `Docker`, go to `192.168.99.100` instead.


### Code Scaffolding
This is using `php artisan make:<item> <name> <arguments>` command.

Where:
- item - is the type of code being generated
- arguments - (optional) specific parameters in generating the code
- name - name of the file being generated

| Task              | Description                                            |
| ----------------- | ------------------------------------------------------ |
| `make:auth`       | Scaffold basic login and registration views and routes |
| `make:controller` | Creates a new controller class                         |
| `make:migration`  | Creates a new migration file                           |
| `make:model`      | Create a new Eloquent model class                      |
| `make:seeder`     | Create a new seeder class                              |
| `make:test`       | Create a new test class                                |

### Project Structure
    .
    ├── app/                     # Project source code
    │   ├── Exceptions           # Exception handlers
    │   ├── Http                 # Request handlers
    │   │   ├── Controllers/     # Controllers
    │   │   └── Middleware/      # Middleware
    │   └── ...                  # Other project components
    ├── bootstrap/               # Project initializers
    │   ├── app.php              # Framework bootstrapper
    │   └── helpers.php          # Helper functions
    ├── database/                # Database functions
    ├── docker/                  # Docker functions
    │   ├── php-config/          # PHP settings for docker
    │   ├── sites/               # Nginx sites for docker
    │   ├── nginx.dockerfile     # Nginx container docker file
    │   ├── php.dockerfile       # PHP container docker file
    │   └── ...                  # Other docker files
    ├── public/                  # Project entry point
    ├── resources/               # Project assets folder
    ├── routes/                  # Route definitions
    ├── storage/                 # Project cache directory
    ├── tests/                   # Project testing
    ├── .env.example             # Environmental variables template
    ├── docker-compose.yml       # Main docker file
    ├── phpunit.xml              # Project testing configuration
    ├── Procfile                 # Heroku process file
    └── ...                      # Other project files

### Testing the project
_Add info here_

## Built with
* <img width=20 height=20 src="https://lumen.laravel.com/img/favicons/favicon-32x32.png"> [Lumen 5.8](https://lumen.laravel.com/) - Web Framework
* <img width=20 height=20 src="https://laravel.com/favicon.png"> [Laravel 5.8](https://laravel.com/) - Core Framework
* <img width=20 height=20 src="https://www.php.net/favicon.ico"> [PHP 7.2](https://php.net/) - Language syntax
* <img width=20 height=20 src="https://www.mongodb.com/assets/images/global/favicon.ico"> [MongoDB Atlas](https://www.mongodb.com/cloud/atlas) - Database
* <img width=20 height=20 src="https://www.docker.com/sites/default/files/d8/Docker-R-Logo-08-2018-Monochomatic-RGB_Moby-x1.png"> [Docker](https://www.docker.com/) - Container platform
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](https://www.heroku.com/) - Hosting and Continuous Integration (CI) service
* <img width=20 height=20 src="https://getcomposer.org/favicon.ico"> [Composer](https://getcomposer.org/) - Package Manager
* <img width=20 height=20 src="https://restfulapi.net/wp-content/uploads/rest.png"> [RESTful API](https://restfulapi.net/) - HTTP Requests Architecture

<!-- * <img width=20 height=20 src="https://miro.medium.com/max/538/1*RPgZ7cp4H77ldoLasm7ueA.png"> [PHPUnit](https://phpunit.de/index.html) - Testing framework -->
<!-- * <img width=20 height=20 src="https://www.sonarqube.org/favicon.ico"> [Codecov](https://www.sonarqube.org/) - Code Inspection and Reliability -->
<!-- * <img width=20 height=20 src="https://codecov.io/static/favicons/favicon-32x32.png"> [Codecov](https://codecov.io/) - Code Coverage -->

## Deployed to
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](http://rin-anidb.herokuapp.com)
