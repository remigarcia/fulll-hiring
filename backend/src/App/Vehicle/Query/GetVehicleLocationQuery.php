<?php

declare(strict_types=1);

namespace Fulll\App\Vehicle\Query;

final readonly class GetVehicleLocationQuery
{
    public function __construct(
        public string $plateNumber,
    ) {}
}
