<?php

declare(strict_types=1);

namespace Fulll\App\Vehicle\Query;

use Fulll\Domain\Vehicle\Location;
use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;

final readonly class GetVehicleLocationHandler
{
    public function __construct(
        private VehicleRepositoryInterface $vehicleRepository,
    ) {}

    public function __invoke(GetVehicleLocationQuery $query): ?Location
    {
        return $this->vehicleRepository->find(new PlateNumber($query->plateNumber))?->location();
    }
}
