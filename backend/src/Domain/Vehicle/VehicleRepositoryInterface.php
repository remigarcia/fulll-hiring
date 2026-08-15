<?php

declare(strict_types=1);

namespace Fulll\Domain\Vehicle;

interface VehicleRepositoryInterface
{
    public function save(Vehicle $vehicle): void;

    public function find(PlateNumber $plateNumber): ?Vehicle;
}
