<?php

namespace App\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsMiddleware;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Connects to the database over TLS when DB_SSL_CA names a CA certificate.
 *
 * Wodby mounts a managed database server's CA certificate and sets DB_SSL_CA
 * to its path; for other databases the variable is empty and connections stay
 * unchanged. The value is read when the connection opens, so the same build
 * works with and without the certificate.
 */
#[AsMiddleware]
final class DatabaseTlsMiddleware implements Middleware
{
    public function __construct(
        #[Autowire('%env(default::DB_SSL_CA)%')]
        private readonly ?string $caFile = null,
    ) {
    }

    public function wrap(Driver $driver): Driver
    {
        $caFile = $this->caFile;
        if (null === $caFile || '' === $caFile || !is_file($caFile)) {
            return $driver;
        }

        return new class($driver, $caFile) extends AbstractDriverMiddleware {
            public function __construct(Driver $driver, private readonly string $caFile)
            {
                parent::__construct($driver);
            }

            public function connect(array $params): Connection
            {
                return parent::connect(DatabaseTlsMiddleware::withCa($params, $this->caFile));
            }
        };
    }

    /**
     * Adds the CA certificate to connection parameters of the drivers that
     * support it. Settings already present in the parameters are kept.
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public static function withCa(array $params, string $caFile): array
    {
        switch ($params['driver'] ?? null) {
            case 'pdo_mysql':
                $option = \defined('Pdo\Mysql::ATTR_SSL_CA') ? \constant('Pdo\Mysql::ATTR_SSL_CA') : \PDO::MYSQL_ATTR_SSL_CA;
                $params['driverOptions'] ??= [];
                $params['driverOptions'][$option] ??= $caFile;
                break;
            case 'mysqli':
                $params['ssl_ca'] ??= $caFile;
                break;
            case 'pdo_pgsql':
            case 'pgsql':
                $params['sslmode'] ??= 'verify-ca';
                $params['sslrootcert'] ??= $caFile;
                break;
        }

        return $params;
    }
}
