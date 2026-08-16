<?php

declare(strict_types=1);

namespace Fulll\Infra\Postgres;

use Fulll\Domain\Fleet\Fleet;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Fleet\FleetRepositoryInterface;

final readonly class PostgresFleetRepository implements FleetRepositoryInterface
{
    public function __construct(private \PDO $pdo) {}

    public function save(Fleet $fleet): void
    {
        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare(
                'INSERT INTO fleets (id, user_id) VALUES (:id, :user_id)
                 ON CONFLICT (id) DO NOTHING',
            );
            $statement->execute(['id' => $fleet->id()->value(), 'user_id' => $fleet->userId()]);

            $delete = $this->pdo->prepare('DELETE FROM fleet_vehicles WHERE fleet_id = :fleet_id');
            $delete->execute(['fleet_id' => $fleet->id()->value()]);

            $insert = $this->pdo->prepare(
                'INSERT INTO fleet_vehicles (fleet_id, plate_number) VALUES (:fleet_id, :plate_number)',
            );
            foreach ($fleet->vehiclePlateNumbers() as $plateNumber) {
                $insert->execute(['fleet_id' => $fleet->id()->value(), 'plate_number' => $plateNumber]);
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function find(FleetId $id): ?Fleet
    {
        $statement = $this->pdo->prepare('SELECT user_id FROM fleets WHERE id = :id');
        $statement->execute(['id' => $id->value()]);
        $userId = $statement->fetchColumn();
        if (!is_string($userId)) {
            return null;
        }

        $plates = $this->pdo->prepare('SELECT plate_number FROM fleet_vehicles WHERE fleet_id = :fleet_id');
        $plates->execute(['fleet_id' => $id->value()]);

        /** @var list<string> $plateNumbers */
        $plateNumbers = $plates->fetchAll(\PDO::FETCH_COLUMN);

        return Fleet::rehydrate($id, $userId, $plateNumbers);
    }
}
