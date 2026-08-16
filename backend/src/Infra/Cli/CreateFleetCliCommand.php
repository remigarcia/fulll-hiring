<?php

declare(strict_types=1);

namespace Fulll\Infra\Cli;

use Fulll\App\Fleet\Command\CreateFleetCommand;
use Fulll\App\Fleet\Command\CreateFleetHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateFleetCliCommand extends Command
{
    public function __construct(private readonly CreateFleetHandler $createFleet)
    {
        parent::__construct('create');
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a fleet and print its id')
            ->addArgument('userId', InputArgument::REQUIRED, 'Owner of the fleet');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $userId */
        $userId = $input->getArgument('userId');

        try {
            $fleetId = ($this->createFleet)(new CreateFleetCommand($userId));
        } catch (\InvalidArgumentException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln($fleetId->value());

        return Command::SUCCESS;
    }
}
