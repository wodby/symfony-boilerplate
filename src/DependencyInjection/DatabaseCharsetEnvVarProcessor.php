<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

/**
 * Resolves "%env(database_charset:DATABASE_URL)%" to the connection charset
 * that suits the URL's database: utf8mb4 for MySQL and MariaDB, utf8 for
 * PostgreSQL, and null for anything else.
 *
 * Wodby's DATABASE_URL carries neither a charset nor a server version.
 * Without both, DoctrineBundle asks the PostgreSQL driver for a platform with
 * an empty version and fails; an explicit charset skips that check, and the
 * server version is then read from the server when the connection opens.
 * A charset in the URL's query string still takes precedence.
 */
final class DatabaseCharsetEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv(string $prefix, string $name, \Closure $getEnv): ?string
    {
        $url = $getEnv($name);
        $scheme = \is_string($url) ? strtolower((string) parse_url($url, \PHP_URL_SCHEME)) : '';

        return match (str_replace('-', '_', $scheme)) {
            'mysql', 'mysql2', 'mariadb', 'pdo_mysql', 'mysqli' => 'utf8mb4',
            'postgres', 'postgresql', 'pgsql', 'pdo_pgsql' => 'utf8',
            default => null,
        };
    }

    public static function getProvidedTypes(): array
    {
        return ['database_charset' => 'string'];
    }
}
