<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Command;

final readonly class RegisterVehicleCommand
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
    ) {}
}
