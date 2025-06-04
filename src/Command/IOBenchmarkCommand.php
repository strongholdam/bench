<?php

namespace Stronghold\Bench\Command;

require_once __DIR__.'/../../src/config/config.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Stronghold\Bench\IO;

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

        // Run the benchmark
        $results = $ioBenchmark->run();

        // Return success
        return Command::SUCCESS;
    }
}
