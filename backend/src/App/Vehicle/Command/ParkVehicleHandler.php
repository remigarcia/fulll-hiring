<?php

declare(strict_types=1);

namespace Fulll\App\Vehicle\Command;

use Fulll\Domain\Fleet\Exception\FleetNotFoundException;
use Fulll\Domain\Fleet\Exception\VehicleNotRegisteredException;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;
use Fulll\Domain\Vehicle\Location;
use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;

final readonly class ParkVehicleHandler
{
    public function __construct(
        private FleetRepositoryInterface $fleetRepository,
        private VehicleRepositoryInterface $vehicleRepository,
    ) {}

    public function __invoke(ParkVehicleCommand $command): void
    {
        $fleetId = FleetId::fromString($command->fleetId);
        $fleet = $this->fleetRepository->find($fleetId) ?? throw new FleetNotFoundException($fleetId);

        $plateNumber = new PlateNumber($command->plateNumber);
        if (!$fleet->hasVehicle($plateNumber)) {
            throw new VehicleNotRegisteredException($plateNumber, $fleetId);
        }

        $vehicle = $this->vehicleRepository->find($plateNumber)
            ?? throw new \RuntimeException(sprintf("Vehicle '%s' is registered but cannot be loaded.", $plateNumber->value()));

        $vehicle->parkAt(new Location($command->latitude, $command->longitude, $command->altitude));
        $this->vehicleRepository->save($vehicle);
    }
}
