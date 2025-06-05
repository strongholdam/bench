<?php

namespace Stronghold\Bench\IO;

use DateTime;
use Stronghold\Bench\Utils\Utils;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

use const CHUNK_SIZE;
use const FILE_SIZE_MB;

/**
 * Write test class for I/O benchmark
 */
class Write
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
     * Perform write test
     *
     * @param string $tempFile Path to temporary file
     * @return array<string, float> Test results [speed, seconds]
     */
    public function performTest(string $tempFile): array
    {
        $this->output->writeln("Starting write test...");
        $writeStart = new DateTime();

        $bytesToWrite = FILE_SIZE_MB * 1024 * 1024; // Convert MB to bytes
        $bytesWritten = 0;
        $writeHandle = fopen($tempFile, 'w');

        $data = str_repeat('A', CHUNK_SIZE); // Create a chunk of data to write

        // Initialize progress bar
        $progressBar = new ProgressBar($this->output, $bytesToWrite);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $progressBar->setRedrawFrequency(max(1, $bytesToWrite / 100)); // Update every 1% progress
        $progressBar->start();

        while ($bytesWritten < $bytesToWrite) {
            $written = fwrite($writeHandle, $data);
            $bytesWritten += $written;

            // Update progress bar
            $progressBar->setProgress($bytesWritten);
        }

        // Finish progress bar
        $progressBar->finish();
        $this->output->writeln('');

        fclose($writeHandle);

        $writeEnd = new DateTime();
        $writeDiff = $writeEnd->diff($writeStart);
        $this->output->writeln(sprintf('Wrote %d MB to disk.', FILE_SIZE_MB));
        $this->output->writeln(sprintf('Write test took %s.', Utils::formatTime($writeDiff)));

        $writeSeconds = Utils::calculateSeconds($writeDiff);
        $writeMBps = FILE_SIZE_MB / $writeSeconds;
        $this->output->writeln(sprintf('Write speed: %s', Utils::formatSpeed(FILE_SIZE_MB, $writeSeconds)));
        $this->output->writeln('');

        return [
            'speed' => $writeMBps,
            'seconds' => $writeSeconds,
        ];
    }
}
