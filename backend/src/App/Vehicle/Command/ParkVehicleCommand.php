<?php

declare(strict_types=1);

namespace Fulll\App\Vehicle\Command;

final readonly class ParkVehicleCommand
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
        public float $latitude,
        public float $longitude,
        public ?float $altitude = null,
    ) {}
}
