<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle;

use Fulll\Domain\Vehicle\Exception\VehicleAlreadyParkedException;

final class Vehicle
{
    private ?Location $location = null;

    public function __construct(private readonly PlateNumber $plateNumber) {}

    public function plateNumber(): PlateNumber
    {
        return $this->plateNumber;
    }

    public function location(): ?Location
    {
        return $this->location;
    }

    public function parkAt(Location $location): void
    {
        if ($this->location?->equals($location) === true) {
            throw new VehicleAlreadyParkedException($this->plateNumber);
        }

        $this->location = $location;
    }
}
