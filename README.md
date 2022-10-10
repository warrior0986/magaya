# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/9.x/installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker). 

Clone the repository

    git clone https://github.com/warrior0986/magaya.git

Switch to the repo folder

    cd magaya

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost

## Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Run the database seeder and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh
    
## Docker

To install with [Docker](https://www.docker.com), run following commands:

```
git clone https://github.com/warrior0986/magaya.git
cd magaya
docker compose up --build --force-recreate -d
```
#Testing with Postman
Import the postman collection `Magaya Test.postman_collection.json` into your postman client, in order to make it work you need to run the queues (database), inside your docker container run:

    php artisan queue:work

Almost all the routes are protected by token, so first generate the token with the GET token request and saved into the collection Bearer token option.

# Testing API

Inside the docker container run

    php artisan test --coverage