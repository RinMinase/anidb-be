<h1 align="center"> Rin Minase's Anime Database<br>(Back-end API Service) </h1>

<p align="center">
    <a href="">
        <img alt="Heroku" src="https://rin-heroku-badge.herokuapp.com/?app=rin-anidb&svg=1&root=deploy-status">
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
        <img alt="Lumen" src="https://img.shields.io/badge/lumen-%5E5.8.10-red.svg?logo=laravel&logoColor=white">
    </a>
    <a href="https://php.net/">
        <img alt="PHP" src="https://img.shields.io/badge/php-7.2.19-blue.svg?logo=php&logoColor=white">
    </a>
</p>

## Introduction
_Add info here_

## Getting Started

### Environmental variables setup
1. MongoDB
    - Fire up your browser and login your [MongoDB Atlas](https://cloud.mongodb.com/user#/atlas/login) account. If you have no account yet, [create one](https://cloud.mongodb.com/user#/atlas/register/accountProfile).
    - If you have no databases yet, create one and name it with anything you like.
    - Once you have a database already, click the `connect` button, and select `Connect your application`
    - Place the parts of the `Connection String` in you environmental variables as follows:

        ```
        Connection String:
        mongodb+srv://<username>:<password>@<cluster>/<database>?retryWrites=true&w=majority

        DB_USERNAME = <username>
        DB_PASSWORD = <password>
        DB_CLUSTER = <cluster>
        DB_DATABASE = <database> or the name of the database you created
        ```

2. Firebase (Firebase Storage)
    - Fire up your browser and login your [Firebase/Google Account](https://console.firebase.google.com). If you have no account yet, [create one](https://accounts.google.com/signup/v2/webcreateaccount?flowEntry=SignUp&flowName=GlifWebSignIn).
    - If you have no Firebase projects yet, create one and name it with anything you like.
    - Once you have a project already, navigate to your project, then to the `settings page` (cog icon) on your top left near Project Overview
    - Once you are inside your settings page, if you already have an app connected to this project, skip this step, otherwise:
        - Click on `Add app` and click on `Web`
        - Register a name for your app, then submit it
    - Go back to firebase settings page, then navigate to `Service Accounts`
    - Click on `Generate new private key` button, then click `Generate Key` on the popup.
    - Open the downloaded JSON file and copy these specific values to your ENV file:

        ```
        FIRE_PROJECT_ID = project_id
        FIRE_KEY = private_key (note, change all \n to \\n)
        FIRE_EMAIL = client_email
        FIRE_CLIENT_ID = client_id
        ```

3. Web Scraper
    - Set `RELEASE_BASE_URI` to the API of the repository it fetches its list (e.g api.github.com/repos/<UserName>/<Repository>)

4. Mailgun
    - Fire up your browser and login your [Mailgun Account](https://app.mailgun.com). If you have no account yet, [create one](https://signup.mailgun.com/new/signup).
    - After you signup, you will be given an API key and its domain. If not, you can navigate to `Settings > Security & Users > API security`. Domain is in a form of `postmaster@<domain>.mailgun.org`
    - View your Private API Key and copy them over to `MAILGUN_API_KEY` of your env file
    - Navigate to `Sending > Domain Settings > SMTP Credentials`
    - Copy the domain under `Login`. (e.g `postmaster@<domain>.mailgun.org`) to `MAILGUN_DOMAIN` of your env file
    - Place any email you want to send your temporary verification code to in `MAILGUN_TEST_USER` with the format `{user name} <{email address}>`

### Running the project
1. If you are running Windows 10, [download](https://download.docker.com/win/stable/Docker%20for%20Windows%20Installer.exe) and install `Docker for Windows`.

    **Note:** If you're not running Windows 10, use `Docker Toolbox` instead, [download](https://docs.docker.com/toolbox/toolbox_install_windows/#step-2-install-docker-toolbox) and install it. Also make sure that you are also running [vitualization](https://docs.docker.com/toolbox/toolbox_install_windows/#step-1-check-your-version).

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

4. Inside the docker image, copy the env file, install the necessary dependencies and generate the API Key

    ```
    cp .env.example .env
    composer install
    php bootstrap/artisan api:generate
    ```

5. Fire up your browser and go to `localhost`.

    **Note:** If you are using `Docker Toolbox` instead of `Docker`, go to `192.168.99.100` instead.

**Note:**
If you need to access the container run, `docker exec -it anidb bash`

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
This is using `php bootstrap/artisan <task> <arguments>` command.

Where:
- task - refer to the task list below
- arguments - (optional) specific parameters in generating the code

| Task                 | Description                                                              |
| -------------------- | ------------------------------------------------------------------------ |
| `api:generate`       | Generate an API Key and saves it to your ENV file                        |
| `api:generate <len>` | Generate an API Key with a specific length and saves it to your ENV file |

### Project Structure
    .
    ├── app/                     # Project source code
    │   ├── Commands/            # Project artisan commands
    │   ├── Controllers/         # Project controllers
    │   ├── Middleware/          # Project middleware
    │   ├── Models/              # Project models
    ├── bootstrap/               # Project initializers
    │   ├── app.php              # Framework bootstrapper
    │   ├── helpers.php          # Helper functions
    │   └── routes.php           # Route definitions
    ├── docker/                  # Docker functions
    │   ├── php-config/          # PHP settings for docker
    │   ├── sites/               # Nginx sites for docker
    │   ├── nginx.dockerfile     # Nginx container docker file
    │   ├── php.dockerfile       # PHP container docker file
    │   └── ...                  # Other docker files
    ├── public/                  # Project entry point
    ├── tests/                   # Project testing
    │   ├── cases/               # Test cases
    │   └── phpunit.xml          # Testing configuration
    ├── .env.example             # Environmental variables template
    ├── docker-compose.yml       # Main docker file
    ├── Procfile                 # Heroku process file
    └── ...                      # Other project files

### Testing the project
_Add info here_

## Built with
* <img width=20 height=20 src="https://lumen.laravel.com/img/favicons/favicon-32x32.png"> [Lumen 5.8](https://lumen.laravel.com/) - Web Framework
* <img width=20 height=20 src="https://laravel.com/img/favicon/favicon.ico"> [Laravel 5.8](https://laravel.com/) - Core Framework
* <img width=20 height=20 src="https://www.php.net/favicon.ico"> [PHP 7.2](https://php.net/) - Language syntax
* <img width=20 height=20 src="https://www.mongodb.com/assets/images/global/favicon.ico"> [MongoDB Atlas](https://www.mongodb.com/cloud/atlas) - Database
* <img width=20 height=20 src="https://firebase.google.com/favicon.ico"> [Firebase Storage](https://firebase.google.com/) - Image Storage
* <img width=20 height=20 src="https://www.docker.com/sites/default/files/d8/Docker-R-Logo-08-2018-Monochomatic-RGB_Moby-x1.png"> [Docker](https://www.docker.com/) - Container platform
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](https://www.heroku.com/) - Hosting and Continuous Integration (CI) service
* <img width=20 height=20 src="https://app.mailgun.com/assets/pilot/images/favicon.png"> [Mailgun](https://www.mailgun.com/) - Email Service
* <img width=20 height=20 src="https://res.cloudinary.com/rin-minase/image/upload/v1563088072/guzzle.png"> [Guzzle](http://docs.guzzlephp.org/) and [Goutte](https://goutte.readthedocs.io/) - HTTP Request Handler and Web Crawler
* <img width=20 height=20 src="https://res.cloudinary.com/rin-minase/image/upload/v1562606888/phpunit.png"> [PHPUnit](https://phpunit.de/index.html) - Testing framework
* <img width=20 height=20 src="https://restfulapi.net/wp-content/uploads/rest.png"> [RESTful API](https://restfulapi.net/) - HTTP Requests Architecture
* <img width=20 height=20 src="https://getcomposer.org/favicon.ico"> [Composer](https://getcomposer.org/) - Package Manager
<!-- * <img width=20 height=20 src="https://www.sonarqube.org/favicon.ico"> [Codecov](https://www.sonarqube.org/) - Code Inspection and Reliability -->
<!-- * <img width=20 height=20 src="https://codecov.io/static/favicons/favicon-32x32.png"> [Codecov](https://codecov.io/) - Code Coverage -->

## Deployed to
* <img width=20 height=20 src="https://www.herokucdn.com/favicons/favicon.ico"> [Heroku](http://rin-anidb.herokuapp.com)
