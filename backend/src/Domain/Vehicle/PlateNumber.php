<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle;

final class PlateNumber
{
    private readonly string $value;

    public function __construct(string $value)
    {
        $normalized = strtoupper(trim($value));
        if ($normalized === '') {
            throw new \InvalidArgumentException('Plate number cannot be empty.');
        }

        $this->value = $normalized;
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
