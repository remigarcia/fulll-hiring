<?php

declare(strict_types=1);

namespace Fulll\Domain\Fleet;

final class FleetId
{
    private const string PATTERN = '/^[0-9a-f]{32}$/';

    private function __construct(private readonly string $value) {}

    public static function generate(): self
    {
        return new self(bin2hex(random_bytes(16)));
    }

    public static function fromString(string $value): self
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new \InvalidArgumentException(sprintf("'%s' is not a valid fleet id.", $value));
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
