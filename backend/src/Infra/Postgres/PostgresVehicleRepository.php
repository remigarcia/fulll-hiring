<?php

declare(strict_types=1);

namespace Fulll\Infra\Postgres;

use Fulll\Domain\Vehicle\Location;
use Fulll\Domain\Vehicle\PlateNumber;
use Fulll\Domain\Vehicle\Vehicle;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;

final readonly class PostgresVehicleRepository implements VehicleRepositoryInterface
{
    public function __construct(private \PDO $pdo) {}

    public function save(Vehicle $vehicle): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO vehicles (plate_number, latitude, longitude, altitude)
             VALUES (:plate_number, :latitude, :longitude, :altitude)
             ON CONFLICT (plate_number) DO UPDATE
             SET latitude = EXCLUDED.latitude, longitude = EXCLUDED.longitude, altitude = EXCLUDED.altitude',
        );
        $statement->execute([
            'plate_number' => $vehicle->plateNumber()->value(),
            'latitude' => $vehicle->location()?->latitude(),
            'longitude' => $vehicle->location()?->longitude(),
            'altitude' => $vehicle->location()?->altitude(),
        ]);
    }

    public function find(PlateNumber $plateNumber): ?Vehicle
    {
        $statement = $this->pdo->prepare(
            'SELECT latitude, longitude, altitude FROM vehicles WHERE plate_number = :plate_number',
        );
        $statement->execute(['plate_number' => $plateNumber->value()]);

        /** @var array{latitude: float|null, longitude: float|null, altitude: float|null}|false $row */
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }

        $location = ($row['latitude'] !== null && $row['longitude'] !== null)
            ? new Location($row['latitude'], $row['longitude'], $row['altitude'])
            : null;

        return Vehicle::rehydrate($plateNumber, $location);
    }
}
