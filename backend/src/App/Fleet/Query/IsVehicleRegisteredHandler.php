<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Query;

use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;
use Fulll\Domain\Vehicle\PlateNumber;

final readonly class IsVehicleRegisteredHandler
{
    public function __construct(
        private FleetRepositoryInterface $fleetRepository,
    ) {}

    public function __invoke(IsVehicleRegisteredQuery $query): bool
    {
        $fleet = $this->fleetRepository->find(FleetId::fromString($query->fleetId));

        return $fleet !== null && $fleet->hasVehicle(new PlateNumber($query->plateNumber));
    }
}
