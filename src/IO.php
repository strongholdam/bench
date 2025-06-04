<?php

namespace Stronghold\Bench;

use DateTime;
use DateInterval;
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
     * Start time of the benchmark
     */
    private DateTime $start;

    /**
     * End time of the benchmark
     */
    private DateTime $end;

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
        // Start timing the entire benchmark
        $this->start = new DateTime();
        $this->output->writeln("Starting I/O benchmark test...");

        // Create a temporary file for testing
        $this->tempFile = tempnam(sys_get_temp_dir(), 'io_benchmark');
        $this->output->writeln("Created temporary test file: {$this->tempFile}");

        // Perform write test
        $writeResults = $this->writeTest->performTest($this->tempFile);

        // Perform read test (multiple iterations)
        $readResults = $this->readTest->performTest($this->tempFile);

        // Clean up
        unlink($this->tempFile);
        $this->output->writeln("Removed temporary test file.");

        // Calculate total time
        $this->end = new DateTime();
        $diff = $this->end->diff($this->start);
        $totalSeconds = Utils::calculateSeconds($diff);

        // Display summary
        $this->output->writeln('');
        $this->output->writeln(sprintf('Total I/O benchmark took %s.', Utils::formatTime($diff)));

        // Calculate combined I/O speed (write once, read multiple times)
        $totalMB = FILE_SIZE_MB + (FILE_SIZE_MB * $readResults['iterations']);
        $combinedSpeed = $totalMB / $totalSeconds;

        // Summary of both read and write performance
        $this->output->writeln("I/O Performance Summary:");
        $this->output->writeln(sprintf('- Write speed: %s', Utils::formatSpeed(FILE_SIZE_MB, $writeResults['seconds'])));
        $this->output->writeln(sprintf(
                '- Read speed: %s (over %d iterations)',
                Utils::formatSpeed(FILE_SIZE_MB * $readResults['iterations'], $readResults['seconds']),
                $readResults['iterations']
            ));
        $this->output->writeln(sprintf('- Combined I/O speed: %s', Utils::formatSpeed($totalMB, $totalSeconds)));
        $this->output->writeln(sprintf('This calculation took %s.', Utils::formatTime($diff)));

        return [
            'write_results' => $writeResults,
            'read_results' => $readResults,
            'total_seconds' => $totalSeconds,
            'combined_speed' => $combinedSpeed,
            'time_diff' => $diff,
        ];
    }
}
