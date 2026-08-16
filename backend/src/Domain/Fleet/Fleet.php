<?php

declare(strict_types=1);

namespace Fulll\Domain\Fleet;

use Fulll\Domain\Fleet\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\Vehicle;

final class Fleet
{
    /** @var array<string, true> keys are plate numbers */
    private array $vehiclePlates = [];

    private function __construct(
        private readonly FleetId $id,
        private readonly string $userId,
    ) {
        if (trim($userId) === '') {
            throw new \InvalidArgumentException('User id cannot be empty.');
        }
    }

    public static function create(string $userId): self
    {
        return new self(FleetId::generate(), $userId);
    }

    public function id(): FleetId
    {
        return $this->id;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function register(Vehicle $vehicle): void
    {
        if ($this->hasVehicle($vehicle->plateNumber())) {
            throw new VehicleAlreadyRegisteredException($vehicle->plateNumber(), $this->id);
        }

        $this->vehiclePlates[$vehicle->plateNumber()->value()] = true;
    }

    public function hasVehicle(PlateNumber $plateNumber): bool
    {
        return isset($this->vehiclePlates[$plateNumber->value()]);
    }
}
