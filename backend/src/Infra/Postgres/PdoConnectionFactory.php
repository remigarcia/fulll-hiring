<?php

declare(strict_types=1);

namespace Fulll\Infra\Postgres;

final class PdoConnectionFactory
{
    private const string DEFAULT_URL = 'postgres://fleet:fleet@127.0.0.1:5432/fleet';

    public static function fromEnv(): \PDO
    {
        $url = getenv('DATABASE_URL') ?: self::DEFAULT_URL;
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['host'])) {
            throw new \InvalidArgumentException(sprintf("Invalid DATABASE_URL '%s'.", $url));
        }

        return new \PDO(
            sprintf(
                'pgsql:host=%s;port=%d;dbname=%s',
                $parts['host'],
                $parts['port'] ?? 5432,
                ltrim($parts['path'] ?? '', '/'),
            ),
            $parts['user'] ?? '',
            $parts['pass'] ?? '',
        );
    }
}
