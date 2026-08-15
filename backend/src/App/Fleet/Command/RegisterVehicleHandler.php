<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Command;

use Fulll\Domain\Fleet\Exception\FleetNotFoundException;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;
use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\Vehicle;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;

final readonly class RegisterVehicleHandler
{
    public function __construct(
        private FleetRepositoryInterface $fleetRepository,
        private VehicleRepositoryInterface $vehicleRepository,
    ) {}

    public function __invoke(RegisterVehicleCommand $command): void
    {
        $fleetId = FleetId::fromString($command->fleetId);
        $fleet = $this->fleetRepository->find($fleetId) ?? throw new FleetNotFoundException($fleetId);

        $plateNumber = new PlateNumber($command->plateNumber);
        $vehicle = $this->vehicleRepository->find($plateNumber);
        if ($vehicle === null) {
            $vehicle = new Vehicle($plateNumber);
            $this->vehicleRepository->save($vehicle);
        }

        $fleet->register($vehicle);
        $this->fleetRepository->save($fleet);
    }
}
