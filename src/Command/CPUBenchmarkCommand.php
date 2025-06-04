<?php

namespace Stronghold\Bench\Command;

require_once __DIR__.'/../../src/config/config.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Stronghold\Bench\CPU;

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

        // Run the benchmark
        $results = $cpuBenchmark->run();

        // Return success
        return Command::SUCCESS;
    }
}
