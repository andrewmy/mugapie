# MugApie

![Travis CI build status](https://travis-ci.com/andrewmy/mugapie.svg?branch=master)
[![PHPStan enabled](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

This is a sample JSON REST API project implemented using [API Platform](https://api-platform.com).

## Running

1. `docker-compose up` — containers with all the dev dependencies.
2. Visit http://127.0.0.1:1080/api/docs for API documentation

### Docker-sync for some dev performance improvement

1. On macOS 10.14+: `sudo installer -pkg /Library/Developer/CommandLineTools/Packages/macOS_SDK_headers_for_macOS_10.14.pkg
-target /`
2. `docker-sync-stack start`
3. `docker-sync-stack clean` when you're done.

## Preparing data from CLI

`docker-compose exec php_app ./bin/console doctrine:fixtures:load -n`

## API

Visit http://127.0.0.1:1080/api/docs for API documentation with sample requests and responses.

All the monetary values are USD and displayed in cents.

All the endpoints are available without authentication, yolo.

- `GET /api/docs.json`
- `GET /api/users`
- ```
  POST /api/users
  {"nickname": "string"}
  ```
- ```
  PUT /api/users/{id}
  {"nickname": "string"}
  ```
- `GET /api/users/{id}`
- `DELETE /api/users/{id}`

## Typical usage flow

- Create a user;
- Create a product for the user;
- Create an order for the user containing products linked to them — the user should have enough on their balance;
- Go wild with the order contents and shipping while it's on hold, limited by your balance;
- Product updates will impact on hold order costs but are deliberately not checked against the balance;
- Orders are deliberately not deletable — you could say it's a very opinionated abandoned cart;
- Send order to production — checking the balance yet again.

## Testing and quality

- Testing: `docker-compose exec php_app make test`
- Static analysis (PHPStan): `docker-compose exec php_app make phpstan`
- Static analysis (Psalm): `docker-compose exec php_app make psalm`
- All the things: `docker-compose exec php_app make ci`

## Going live

To deploy this on production you would have to make the docker build multistage, leave out the dev tools and tighten up security.
