<?php

namespace Stronghold\Bench\IO;

use DateTime;
use Stronghold\Bench\Utils\Utils;
use Symfony\Component\Console\Output\OutputInterface;

use const CHUNK_SIZE;
use const FILE_SIZE_MB;
use const READ_ITERATIONS;

/**
 * Read test class for I/O benchmark
 */
class Read
{
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
    }

    /**
     * Perform read test
     *
     * @param string $tempFile Path to temporary file
     * @return array<string, float|int> Test results [speed, seconds, iterations]
     */
    public function performTest(string $tempFile): array
    {
        $this->output->writeln("Starting read test...");
        $readStart = new DateTime();

        $totalBytesRead = 0;
        $totalReadTime = 0;
        $fileSize = FILE_SIZE_MB * 1024 * 1024; // Convert MB to bytes

        // Read the file multiple times as specified in READ_ITERATIONS
        for ($iteration = 1; $iteration <= READ_ITERATIONS; $iteration++) {
            $this->output->writeln("Read iteration $iteration of ".READ_ITERATIONS."...");

            $bytesRead = 0;
            $readHandle = fopen($tempFile, 'r');

            $lastDot = microtime(true);
            while (!feof($readHandle)) {
                $chunk = fread($readHandle, CHUNK_SIZE);
                $bytesRead += strlen($chunk);
                if (microtime(true) - $lastDot >= 4.5) {
                    $this->output->write('.');
                    $lastDot = microtime(true);
                }
            }
            $this->output->writeln('');

            fclose($readHandle);
            $totalBytesRead += $bytesRead;

            $this->output->writeln(sprintf('Read %d MB from disk in iteration %d.', FILE_SIZE_MB, $iteration));
        }

        $readEnd = new DateTime();
        $readDiff = $readEnd->diff($readStart);
        $this->output->writeln('');
        $this->output->writeln(sprintf('Read %d MB from disk in %d iterations.', FILE_SIZE_MB, READ_ITERATIONS));
        $this->output->writeln(sprintf('Read test took %s.', Utils::formatTime($readDiff)));

        $readSeconds = Utils::calculateSeconds($readDiff);
        $readMBps = (FILE_SIZE_MB * READ_ITERATIONS) / $readSeconds;
        $this->output->writeln(sprintf('Read speed: %s', Utils::formatSpeed(FILE_SIZE_MB * READ_ITERATIONS, $readSeconds)));
        $this->output->writeln('');

        return [
            'speed' => $readMBps,
            'seconds' => $readSeconds,
            'iterations' => READ_ITERATIONS,
        ];
    }
}
