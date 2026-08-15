<?php

declare(strict_types=1);

namespace Fulll\App\Fleet\Command;

use Fulll\Domain\Fleet\Fleet;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;

final readonly class CreateFleetHandler
{
    public function __construct(
        private FleetRepositoryInterface $fleetRepository,
    ) {}

    public function __invoke(CreateFleetCommand $command): FleetId
    {
        $fleet = Fleet::create($command->userId);
        $this->fleetRepository->save($fleet);

        return $fleet->id();
    }
}
