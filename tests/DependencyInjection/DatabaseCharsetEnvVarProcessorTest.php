<?php

namespace App\Tests\DependencyInjection;

use App\DependencyInjection\DatabaseCharsetEnvVarProcessor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DatabaseCharsetEnvVarProcessorTest extends TestCase
{
    /**
     * @return iterable<string, array{string, ?string}>
     */
    public static function urls(): iterable
    {
        yield 'mysql' => ['mysql://app:p%40ss@mariadb:3306/app', 'utf8mb4'];
        yield 'postgresql' => ['postgresql://app:p%40ss@postgres:5432/app', 'utf8'];
        yield 'pgsql' => ['pgsql://app@postgres/app', 'utf8'];
        yield 'sqlite' => ['sqlite:///var/data.db', null];
        yield 'empty' => ['', null];
    }

    #[DataProvider('urls')]
    public function testCharsetFollowsTheScheme(string $url, ?string $charset): void
    {
        $processor = new DatabaseCharsetEnvVarProcessor();

        self::assertSame($charset, $processor->getEnv('database_charset', 'DATABASE_URL', static fn () => $url));
    }
}
