# Symfony starter for Wodby

A production-ready starting point for the
[Wodby Symfony service](https://github.com/wodby/service-symfony-php) and
[Symfony stack](https://github.com/wodby/stack-symfony).

The starter follows the official Symfony 7.4 LTS skeleton and includes:

- a responsive deployment-status landing page
- a lightweight health endpoint at `/healthz`
- Doctrine ORM and migrations, Twig, Mailer and Monolog
- Maker and the web profiler for development
- tests for the landing page and health endpoint
- GitHub Actions tests on PHP 8.4 and 8.5
- a ready-to-use Wodby CI pipeline at `.wodby/pipeline.yml`

## Local development

Install PHP 8.4 or newer and Composer, then run:

```shell
composer install
php -S localhost:8000 -t public
```

Open http://localhost:8000. The health endpoint is available at
http://localhost:8000/healthz.

The database defaults to PostgreSQL on `127.0.0.1`. Put your own connection in
an uncommitted `.env.local`:

```shell
DATABASE_URL="postgresql://app:secret@127.0.0.1:5432/app"
```

Run the application tests with:

```shell
php bin/phpunit
```

## Start building

The landing page and health endpoint live in
`src/Controller/StarterController.php`, with the page template in
`templates/starter/index.html.twig`. Replace them as your application takes
shape.

The Maker bundle creates the first pieces of your domain:

```shell
php bin/console make:controller ProductController
php bin/console make:entity Product
php bin/console make:migration
```

Wodby runs `doctrine:migrations:migrate` after every deployment, so committed
migrations are applied automatically.

## On Wodby

Wodby supplies `APP_ENV`, `APP_SECRET`, `DATABASE_URL`, `MAILER_DSN` and the
trusted proxy settings to deployed applications. Real environment variables
take precedence over the committed `.env` files, so keep secrets out of the
repository and configure additional values through the Wodby application
environment.

- Environments of type `dev` run with `APP_ENV=dev`; every other type runs
  with `APP_ENV=prod`.
- `DEFAULT_URI`, used for URLs generated outside a request, follows the
  environment's primary URL.
- When the linked database requires TLS, Wodby mounts its CA certificate and
  the application connects with it.

See the [Symfony documentation](https://symfony.com/doc/7.4/index.html) and
[Wodby documentation](https://wodby.com/docs/2.0/) for the next steps.

## Upstream

This repository is maintained from the
[official Symfony skeleton](https://github.com/symfony/skeleton) and its Flex
recipes. Wodby keeps the framework structure conventional while maintaining the
deployment-facing starter experience and validation.

## License

Symfony and this starter are open-sourced software licensed under the
[MIT license](LICENSE).
