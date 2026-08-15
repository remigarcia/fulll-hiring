<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Query;

final readonly class IsVehicleRegisteredQuery
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
    ) {}
}
