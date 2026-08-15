<?php

declare(strict_types=1);

namespace Fulll\Infra\Fleet;

use Fulll\Domain\Fleet\Fleet;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;

final class InMemoryFleetRepository implements FleetRepositoryInterface
{
    /** @var array<string, Fleet> */
    private array $fleets = [];

    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->id()->value()] = $fleet;
    }

    public function find(FleetId $id): ?Fleet
    {
        return $this->fleets[$id->value()] ?? null;
    }
}
