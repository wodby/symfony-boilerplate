<?php

namespace App\Tests\Doctrine;

use App\Doctrine\DatabaseTlsMiddleware;
use Doctrine\DBAL\Driver;
use PHPUnit\Framework\TestCase;

final class DatabaseTlsMiddlewareTest extends TestCase
{
    public function testMysqlGetsTheCaCertificate(): void
    {
        $option = \defined('Pdo\Mysql::ATTR_SSL_CA') ? \constant('Pdo\Mysql::ATTR_SSL_CA') : \PDO::MYSQL_ATTR_SSL_CA;
        $params = DatabaseTlsMiddleware::withCa(['driver' => 'pdo_mysql'], '/etc/wodby/db/ca.pem');

        self::assertSame([$option => '/etc/wodby/db/ca.pem'], $params['driverOptions']);
    }

    public function testPostgresVerifiesTheServer(): void
    {
        $params = DatabaseTlsMiddleware::withCa(['driver' => 'pdo_pgsql'], '/etc/wodby/db/ca.pem');

        self::assertSame('verify-ca', $params['sslmode']);
        self::assertSame('/etc/wodby/db/ca.pem', $params['sslrootcert']);
    }

    public function testExplicitSettingsAreKept(): void
    {
        $params = DatabaseTlsMiddleware::withCa(['driver' => 'pdo_pgsql', 'sslmode' => 'require'], '/etc/wodby/db/ca.pem');

        self::assertSame('require', $params['sslmode']);
    }

    public function testWithoutCertificateTheDriverIsUnchanged(): void
    {
        $driver = $this->createStub(Driver::class);

        self::assertSame($driver, (new DatabaseTlsMiddleware(''))->wrap($driver));
        self::assertSame($driver, (new DatabaseTlsMiddleware('/nonexistent/ca.pem'))->wrap($driver));
    }

    public function testWithCertificateTheDriverIsWrapped(): void
    {
        $driver = $this->createStub(Driver::class);

        self::assertNotSame($driver, (new DatabaseTlsMiddleware(__FILE__))->wrap($driver));
    }
}
