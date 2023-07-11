# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost/api/doc` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Run fixtures to create dummy data
1. Enter Docker php container
2. Run `bin/console doctrine:fixtures:load  -n` to create fresh dummy data
## Running Tests
1. Enter Docker php container
2. Run `make tests` which will create test database, and run all tests

## Email web interface
1. Open `http://localhost:1080/` in your favorite web browser

