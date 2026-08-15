<?php

declare(strict_types=1);

namespace Fulll\Infra\Vehicle;

use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\Vehicle;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;

final class InMemoryVehicleRepository implements VehicleRepositoryInterface
{
    /** @var array<string, Vehicle> */
    private array $vehicles = [];

    public function save(Vehicle $vehicle): void
    {
        $this->vehicles[$vehicle->plateNumber()->value()] = $vehicle;
    }

    public function find(PlateNumber $plateNumber): ?Vehicle
    {
        return $this->vehicles[$plateNumber->value()] ?? null;
    }
}
