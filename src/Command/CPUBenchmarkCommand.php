<?php

namespace Stronghold\Bench\Command;

require_once __DIR__.'/../../src/config/config.php';

use Stronghold\Bench\CPU\CPU;
use Stronghold\Bench\Tracker\TimeTracker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CPUBenchmarkCommand extends Command
{
    protected static $defaultName = 'benchmark:cpu';
    protected static $defaultDescription = 'Run the CPU benchmark';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->setHelp('This command runs a CPU benchmark by calculating prime numbers');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting CPU benchmark...');

        // Create a new CPU benchmark instance with the output interface
        $cpuBenchmark = new CPU($output);

        // Create a new execution time tracker
        $timeTracker = new TimeTracker($output);

        // Run the benchmark and track execution time
        $results = $timeTracker->trackExecutionTime(self::$defaultName, function () use ($cpuBenchmark) {
            return $cpuBenchmark->run();
        });

        // Return success
        return Command::SUCCESS;
    }
}
