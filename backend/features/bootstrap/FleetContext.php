<?php

declare(strict_types=1);

namespace Fulll\Features;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Fulll\App\Fleet\Command\CreateFleetCommand;
use Fulll\App\Fleet\Command\CreateFleetHandler;
use Fulll\App\Vehicle\Command\ParkVehicleCommand;
use Fulll\App\Vehicle\Command\ParkVehicleHandler;
use Fulll\App\Fleet\Command\RegisterVehicleCommand;
use Fulll\App\Fleet\Command\RegisterVehicleHandler;
use Fulll\App\Vehicle\Query\GetVehicleLocationHandler;
use Fulll\App\Vehicle\Query\GetVehicleLocationQuery;
use Fulll\App\Fleet\Query\IsVehicleRegisteredHandler;
use Fulll\App\Fleet\Query\IsVehicleRegisteredQuery;
use Fulll\Domain\Vehicle\Exception\VehicleAlreadyParkedException;
use Fulll\Domain\Fleet\Exception\VehicleAlreadyRegisteredException;
use Fulll\Domain\Fleet\FleetId;
use Fulll\Domain\Vehicle\Location;
use Fulll\Domain\Fleet\FleetRepositoryInterface;
use Fulll\Domain\Vehicle\VehicleRepositoryInterface;
use Fulll\Infra\InMemory\InMemoryFleetRepository;
use Fulll\Infra\Postgres\PostgresFleetRepository;
use Fulll\Infra\Postgres\PdoConnectionFactory;
use Fulll\Infra\InMemory\InMemoryVehicleRepository;
use Fulll\Infra\Postgres\PostgresVehicleRepository;

final class FleetContext implements Context
{
    private CreateFleetHandler $createFleet;
    private RegisterVehicleHandler $registerVehicle;
    private ParkVehicleHandler $parkVehicle;
    private IsVehicleRegisteredHandler $isVehicleRegistered;
    private GetVehicleLocationHandler $getVehicleLocation;

    private ?FleetId $myFleetId = null;
    private ?FleetId $otherFleetId = null;
    private ?string $plateNumber = null;
    private ?Location $location = null;
    private ?\DomainException $caughtException = null;

    public function __construct(string $driver = 'memory')
    {
        [$fleetRepository, $vehicleRepository] = match ($driver) {
            'memory' => [new InMemoryFleetRepository(), new InMemoryVehicleRepository()],
            'postgres' => self::postgresRepositories(),
            default => throw new \InvalidArgumentException(sprintf("Unknown driver '%s'.", $driver)),
        };

        $this->createFleet = new CreateFleetHandler($fleetRepository);
        $this->registerVehicle = new RegisterVehicleHandler($fleetRepository, $vehicleRepository);
        $this->parkVehicle = new ParkVehicleHandler($fleetRepository, $vehicleRepository);
        $this->isVehicleRegistered = new IsVehicleRegisteredHandler($fleetRepository);
        $this->getVehicleLocation = new GetVehicleLocationHandler($vehicleRepository);
    }

    /**
     * Behat builds a fresh context per scenario: truncating here isolates scenarios on the shared database.
     *
     * @return array{FleetRepositoryInterface, VehicleRepositoryInterface}
     */
    private static function postgresRepositories(): array
    {
        $pdo = PdoConnectionFactory::fromEnv();
        $pdo->exec('TRUNCATE fleet_vehicles, fleets, vehicles');

        return [new PostgresFleetRepository($pdo), new PostgresVehicleRepository($pdo)];
    }

    #[Given('my fleet')]
    public function myFleet(): void
    {
        $this->myFleetId = ($this->createFleet)(new CreateFleetCommand('user-1'));
    }

    #[Given('the fleet of another user')]
    public function theFleetOfAnotherUser(): void
    {
        $this->otherFleetId = ($this->createFleet)(new CreateFleetCommand('user-2'));
    }

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->plateNumber = 'AA-123-BC';
    }

    #[When('I register this vehicle into my fleet')]
    #[Given('I have registered this vehicle into my fleet')]
    public function iRegisterThisVehicleIntoMyFleet(): void
    {
        ($this->registerVehicle)(new RegisterVehicleCommand($this->myFleetId()->value(), $this->plateNumber()));
    }

    #[Given("this vehicle has been registered into the other user's fleet")]
    public function thisVehicleHasBeenRegisteredIntoTheOtherUsersFleet(): void
    {
        ($this->registerVehicle)(new RegisterVehicleCommand($this->otherFleetId()->value(), $this->plateNumber()));
    }

    #[When('I try to register this vehicle into my fleet')]
    public function iTryToRegisterThisVehicleIntoMyFleet(): void
    {
        try {
            $this->iRegisterThisVehicleIntoMyFleet();
        } catch (\DomainException $exception) {
            $this->caughtException = $exception;
        }
    }

    #[Then('this vehicle should be part of my vehicle fleet')]
    public function thisVehicleShouldBePartOfMyVehicleFleet(): void
    {
        $isRegistered = ($this->isVehicleRegistered)(
            new IsVehicleRegisteredQuery($this->myFleetId()->value(), $this->plateNumber()),
        );

        $this->assertThat($isRegistered, 'Vehicle is expected to be part of the fleet.');
    }

    #[Then('I should be informed this this vehicle has already been registered into my fleet')]
    public function iShouldBeInformedVehicleAlreadyRegistered(): void
    {
        $this->assertCaught(VehicleAlreadyRegisteredException::class);
    }

    #[Given('a location')]
    public function aLocation(): void
    {
        $this->location = new Location(48.8566, 2.3522);
    }

    #[When('I park my vehicle at this location')]
    #[Given('my vehicle has been parked into this location')]
    public function iParkMyVehicleAtThisLocation(): void
    {
        ($this->parkVehicle)(new ParkVehicleCommand(
            $this->myFleetId()->value(),
            $this->plateNumber(),
            $this->location()->latitude(),
            $this->location()->longitude(),
            $this->location()->altitude(),
        ));
    }

    #[When('I try to park my vehicle at this location')]
    public function iTryToParkMyVehicleAtThisLocation(): void
    {
        try {
            $this->iParkMyVehicleAtThisLocation();
        } catch (\DomainException $exception) {
            $this->caughtException = $exception;
        }
    }

    #[Then('the known location of my vehicle should verify this location')]
    public function theKnownLocationOfMyVehicleShouldVerifyThisLocation(): void
    {
        $knownLocation = ($this->getVehicleLocation)(new GetVehicleLocationQuery($this->plateNumber()));

        $this->assertThat(
            $knownLocation !== null && $knownLocation->equals($this->location()),
            'Vehicle location does not verify the expected location.',
        );
    }

    #[Then('I should be informed that my vehicle is already parked at this location')]
    public function iShouldBeInformedVehicleAlreadyParked(): void
    {
        $this->assertCaught(VehicleAlreadyParkedException::class);
    }

    private function myFleetId(): FleetId
    {
        return $this->myFleetId ?? throw new \LogicException('No fleet in the current scenario.');
    }

    private function otherFleetId(): FleetId
    {
        return $this->otherFleetId ?? throw new \LogicException('No other user fleet in the current scenario.');
    }

    private function plateNumber(): string
    {
        return $this->plateNumber ?? throw new \LogicException('No vehicle in the current scenario.');
    }

    private function location(): Location
    {
        return $this->location ?? throw new \LogicException('No location in the current scenario.');
    }

    private function assertThat(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }

    /**
     * @param class-string<\DomainException> $expectedClass
     */
    private function assertCaught(string $expectedClass): void
    {
        $this->assertThat(
            $this->caughtException instanceof $expectedClass,
            sprintf(
                'Expected %s, got %s.',
                $expectedClass,
                $this->caughtException === null ? 'no exception' : $this->caughtException::class,
            ),
        );
    }
}
