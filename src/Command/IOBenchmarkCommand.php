<?php

namespace Stronghold\Bench\Command;

require_once __DIR__.'/../../src/config/config.php';

use Stronghold\Bench\IO\IO;
use Stronghold\Bench\Tracker\TimeTracker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IOBenchmarkCommand extends Command
{
    protected static $defaultName = 'benchmark:io';
    protected static $defaultDescription = 'Run the I/O benchmark';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription)
            ->setHelp('This command runs an I/O benchmark by writing and reading a large file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting I/O benchmark...');

        // Create a new IO benchmark instance with the output interface
        $ioBenchmark = new IO($output);

        // Create a new execution time tracker
        $timeTracker = new TimeTracker($output);

        // Run the benchmark and track execution time
        $results = $timeTracker->trackExecutionTime(self::$defaultName, function () use ($ioBenchmark) {
            return $ioBenchmark->run();
        });

        // Return success
        return Command::SUCCESS;
    }
}
