<?php

declare(strict_types=1);

namespace Fulll\Infra\Cli;

use Fulll\App\Vehicle\Command\ParkVehicleCommand;
use Fulll\App\Vehicle\Command\ParkVehicleHandler;
use Fulll\Domain\Fleet\Exception\FleetNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LocalizeVehicleCliCommand extends Command
{
    public function __construct(private readonly ParkVehicleHandler $parkVehicle)
    {
        parent::__construct('localize-vehicle');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Record the location of a vehicle of a fleet')
            ->addArgument('fleetId', InputArgument::REQUIRED)
            ->addArgument('vehiclePlateNumber', InputArgument::REQUIRED)
            ->addArgument('lat', InputArgument::REQUIRED)
            ->addArgument('lng', InputArgument::REQUIRED)
            ->addArgument('alt', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $fleetId */
        $fleetId = $input->getArgument('fleetId');
        /** @var string $plateNumber */
        $plateNumber = $input->getArgument('vehiclePlateNumber');
        /** @var string $latitude */
        $latitude = $input->getArgument('lat');
        /** @var string $longitude */
        $longitude = $input->getArgument('lng');
        /** @var string|null $altitude */
        $altitude = $input->getArgument('alt');

        if (!is_numeric($latitude) || !is_numeric($longitude) || ($altitude !== null && !is_numeric($altitude))) {
            $output->writeln('<error>lat, lng and alt must be numbers.</error>');

            return Command::INVALID;
        }

        try {
            ($this->parkVehicle)(new ParkVehicleCommand(
                $fleetId,
                $plateNumber,
                (float) $latitude,
                (float) $longitude,
                $altitude !== null ? (float) $altitude : null,
            ));
        } catch (\DomainException|\InvalidArgumentException|FleetNotFoundException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln('Vehicle localized.');

        return Command::SUCCESS;
    }
}
