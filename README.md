<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template</h1>
    <br>
</p>

Yii 2 Basic Project Template is a skeleton [Yii 2](https://www.yiiframework.com/) application best for
rapidly creating small projects.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      components/         contains additional classes with extendsion
      config/             contains application configurations
      controllers/web     contains Web controller classes
      controllers/api     contains Api controller classes for REST Api
      docker/             contains all configs and dumps used in docker-compose
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      traits/             contains global traits
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources, .htaccess
      web/api             contains the entry script and Api resources, .htaccess



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 7.4.


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](https://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](https://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer create-project --prefer-dist yiisoft/yii2-app-basic basic
~~~

Now you should be able to access the application through the following URL, assuming `basic` is the directory
directly under the Web root.

~~~
http://localhost/basic/web/
~~~

### Install from an Archive File

Extract the archive file downloaded from [yiiframework.com](https://www.yiiframework.com/download/) to
a directory named `basic` that is directly under the Web root.

Set cookie validation key in `config/web.php` file to some random secret string:

```php
'request' => [
    // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
    'cookieValidationKey' => '<secret random string goes here>',
],
```

You can then access the application through the following URL:

~~~
http://localhost/basic/web/
~~~


### Install with Docker

Update your vendor packages

    docker-compose run --rm php composer update --prefer-dist
    
Run the installation triggers (creating cookie validation code)

    docker-compose run --rm php composer install    
    
Start the container

    docker-compose up -d
    
You can then access the application through the following URL:

    http://127.0.0.1:8054

**NOTES:** 
- Minimum required Docker engine version `17.04` for development (see [Performance tuning for volume mounts](https://docs.docker.com/docker-for-mac/osxfs-caching/))
- The default configuration uses a host-volume in your home directory `.docker-composer` for composer caches


CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


# ABOUT 

Yii2 MongoDB REST API

This project is a RESTful API built with Yii2 and MongoDB that manages users and their tasks. It uses JWT authentication, supports pagination, sorting, filtering, and includes task statistics functionality.

### Tech Stack

#### Backend: Yii2 Framework

#### Database: MongoDB

#### Authentication: JSON Web Token (JWT)

#### Format: JSON responses

#### Environments: WSL 1.0, docker-compose

**NOTES:**
- in docker-compose.yml for volumes we using absolute path (which in our case it's D: directory)
### PLEASE CHANGE VOLUMES BEFORE BUILDING AND RUNNING, for WSL 2.0 you can use relative path:
```yml
volumes:
      - ~/.composer-docker/cache:/root/.composer/cache:delegated
      - ./yii2_project/:/app
```

# Setup Instructions

Clone the repository

Configure your MongoDB connection in config/db.php

Configure the API in config/api.php

Set your JWT secret key in params.php:
```
'jwtSecretKey' => 'your_secret_key_here'
```

Start the server:
```
docker compose up --build 
```

In detached mode:
```
docker compose up --build -d
```
Authentication

JWT must be included in the Authorization header:
```json
Authorization: Bearer <your-token>
```
User Endpoints

Create User
```
POST api/users
```
Body:

```json
{
  "username": "johndoe",
  "password": "Pass123-",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com"
}
```

### List Users
```
GET api/users?page=1&per-page=10&query=John
```

#### Supports: pagination, search, and sorting by first_name, last_name, email

### Get Single User

GET api/users/{id}

### Update User
```
PUT api/users/{id}
```
Body (partial or full):
```json
{
  "first_name": "Johnny"
}
```
### Delete User
```
DELETE api/users/{id}
```
Deletes the user and all associated tasks

## Auth Endpoints

### Register
```
POST api/users
```
### Login
```
POST api/users/login
```

Body:
```json
{
  "username": "johndoe",
  "password": "Pass123-"
}
```
Response includes a JWT token

## Task Endpoints

### Create Task
```
POST api/users/{id}/tasks
```

Body:
```json
{
  "title": "Fix Bug",
  "description": "Fix the login bug."
}
```
### List Tasks
```
GET api/users/{id}/tasks?page=1&per-page=5&query=In Progress
```
Supports: pagination, search, and sorting by title, status

### View Task
```
GET api/users/{id}/tasks/{taskId}
```
### Update Task
```
PUT api/users/{id}/tasks/{taskId}
```
### Delete Task (if status is "New")
```
DELETE api/users/{id}/tasks/{taskId}
```
### Delete All "New" Tasks
```
DELETE api/users/{id}/tasks
```
### Task Statistics

Per User Stats
```
GET api/users/{id}/tasks/stats
```
Response:
```json
{
  "New": 2,
  "In Progress": 1,
  "Done": 3
}
```
### Global Stats
```
GET api/users/{id}/stats-global
```
Aggregated stats across all users

## Notes

Dates are returned in DD-MM-YYYY HH:mm format

All fields are required during creation

Only valid task status transitions are allowed:

New -> In Progress -> Done

New -> In Progress -> Failed




