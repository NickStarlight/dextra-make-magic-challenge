# Dextra's Make Magic Challenge
![Tests](https://github.com/NickStarlight/dextra-make-magic-challenge/workflows/Tests/badge.svg)
![GitHub](https://img.shields.io/github/license/NickStarlight/dextra-make-magic-challenge)
![Language](https://img.shields.io/badge/PHP-8-informational)
![Coding Style](https://img.shields.io/badge/Coding%20Style-PSR--2-lightgrey)

This is my take on the challenge.

## Requeriments

If you're using the included Docker development enviroment:
1. Docker
2. Docker-compose

If you're not using:
1. PHP ^8.0
2. PostgreSQL ^13.0
3. Redis ^6.0
4. Composer ^2.0
5. php8.0-common php8.0-bcmath php8.0-json php8.0-mbstring
6. openssl

## Installation

This project uses Docker for the development enviroment, it works on top of the handy [Laravel Sail](https://laravel.com/docs/8.x/sail) package.

1. Clone the repository:
```bash
git clone https://github.com/NickStarlight/dextra-make-magic-challenge.git
```

2. Configure your .env file:
```bash
cp .env.example .env
php artisan key:generate
```
`Note: Most default values on the .env.example already work out of the box with the development enviroment, but feel free to change them as you please.`

3. Configure your Potter API credentials:
```env
# On .env

POTTER_API_URL=
POTTER_API_SECRET=
POTTER_API_RETRY_COUNT=
POTTER_API_CACHE_LIFESPAN=
```
| Variable  |  Description  |
|---|---|
| POTTER_API_URL  | This is the Potter API fully qualified URL with a / at the end. |
| POTTER_API_SECRET  | This is your secret key, get one here: https://github.com/dextra/challenges/blob/master/backend/MAKE-MAGIC-PT.md#1---cria%C3%A7%C3%A3o-de-usu%C3%A1rio.  |
| POTTER_API_RETRY_COUNT  | This sets the retry policy of the SDK used on this project, on failure, the SDK will retry this many times again before failing.  |
| POTTER_API_CACHE_LIFESPAN  | This sets the cache strategy for the responses received from the API, set a value in seconds, if you don't want cache anything at all, set it to zero.  |

4. Install the base dependencies
```bash
composer install
```

5. Start the development enviroment
```bash
./vendor/bin/sail up
```
`Note: If you get any errors like 'Docker is not running', you're probably running Docker as sudo, and that's not good.https://docs.docker.com/engine/install/linux-postinstall/`

`Note 2: The ports 80(HTTP), 5432(PostgreSQL) and 6379(Redis) should be free on your system since the development enviroment exposes them to the system.`

## Error reporting

By default all errors are supressed, if you would like to enable more detailed error reports, change on your `.env`:

```env
APP_DEBUG=true
```

## Documentation
All responses are formatted to comply with the [JSON:API](https://jsonapi.org/) standard.


The project includes a [Insomnia Workspace](https://support.insomnia.rest/article/50-workspaces) that you can import in order to test and check the routes, just import the `documentation/Insomnia.json` file and you're ready to go.

If you're a more visual person, the `documentation` folder includes a Swagger style HTML documentation, just serve the folder with your favorite HTTP server.

## Testing
All tests can be ran using the Laravel Sail CLI:
```bash
./vendor/bin/sail artisan test
```

## License
[WTFPL](https://choosealicense.com/licenses/wtfpl/)