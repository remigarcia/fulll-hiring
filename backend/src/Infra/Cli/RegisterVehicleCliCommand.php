<?php

declare(strict_types=1);

namespace Fulll\Infra\Cli;

use Fulll\App\Fleet\Command\RegisterVehicleCommand;
use Fulll\App\Fleet\Command\RegisterVehicleHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RegisterVehicleCliCommand extends Command
{
    public function __construct(private readonly RegisterVehicleHandler $registerVehicle)
    {
        parent::__construct('register-vehicle');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Register a vehicle into a fleet')
            ->addArgument('fleetId', InputArgument::REQUIRED)
            ->addArgument('vehiclePlateNumber', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $fleetId */
        $fleetId = $input->getArgument('fleetId');
        /** @var string $plateNumber */
        $plateNumber = $input->getArgument('vehiclePlateNumber');

        try {
            ($this->registerVehicle)(new RegisterVehicleCommand($fleetId, $plateNumber));
        } catch (\DomainException|\InvalidArgumentException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln(sprintf("Vehicle '%s' registered into fleet '%s'.", $plateNumber, $fleetId));

        return Command::SUCCESS;
    }
}
