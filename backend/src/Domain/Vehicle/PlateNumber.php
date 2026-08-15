<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle;

final class PlateNumber
{
    public function __construct(private readonly string $value)
    {
        if (trim($value) === '') {
            throw new \InvalidArgumentException('Plate number cannot be empty.');
        }
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
