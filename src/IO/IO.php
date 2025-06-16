<?php

namespace Stronghold\Bench\IO;

use Stronghold\Bench\Utils\Utils;
use Symfony\Component\Console\Output\OutputInterface;
use const FILE_SIZE_MB;

/**
 * I/O Benchmark Controller Class
 *
 * This class orchestrates the I/O benchmark by:
 * 1. Creating a temporary file
 * 2. Running the write test
 * 3. Running the read test (multiple iterations)
 * 4. Displaying a performance summary
 */
class IO
{
    /**
     * Path to the temporary file
     */
    private string $tempFile;

    /**
     * Write test instance
     */
    private Write $writeTest;

    /**
     * Read test instance
     */
    private Read $readTest;

    /**
     * Output interface for console output
     */
    private OutputInterface $output;

    /**
     * Constructor
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
        $this->writeTest = new Write($output);
        $this->readTest = new Read($output);
    }

    /**
     * Run the I/O benchmark
     *
     * @return array<string, mixed> Results of the benchmark
     */
    public function run(): array
    {
        $this->output->writeln("Starting I/O benchmark test...");

        // Create a temporary file for testing
        $this->tempFile = sprintf('%s/io_benchmark_file', sys_get_temp_dir());
        $this->output->writeln("Created temporary test file: {$this->tempFile}");

        // Perform write test
        $writeResults = $this->writeTest->performTest($this->tempFile);

        // Perform read test (multiple iterations)
        $readResults = $this->readTest->performTest($this->tempFile);

        // Clean up
        unlink($this->tempFile);
        $this->output->writeln("Removed temporary test file.");

        // Display summary
        $this->output->writeln('');

        // Summary of both read and write performance
        $this->output->writeln("I/O Performance Summary:");
        $this->output->writeln(sprintf('- Write speed: %s', Utils::formatSpeed(FILE_SIZE_MB, $writeResults['seconds'])));
        $this->output->writeln(
            sprintf(
                '- Read speed: %s (over %d iterations)',
                Utils::formatSpeed(FILE_SIZE_MB * $readResults['iterations'], $readResults['seconds']),
                $readResults['iterations']
            )
        );

        return [
            'write_results' => $writeResults,
            'read_results' => $readResults,
        ];
    }
}
