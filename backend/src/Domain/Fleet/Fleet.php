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

    /**
     * @param list<string> $plateNumbers
     */
    public static function rehydrate(FleetId $id, string $userId, array $plateNumbers): self
    {
        $fleet = new self($id, $userId);
        foreach ($plateNumbers as $plateNumber) {
            $fleet->vehiclePlates[new PlateNumber($plateNumber)->value()] = true;
        }

        return $fleet;
    }

    /**
     * @return list<string>
     */
    public function vehiclePlateNumbers(): array
    {
        return array_keys($this->vehiclePlates);
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
