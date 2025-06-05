<?php

namespace Stronghold\Bench\Tracker;

use DateInterval;
use DateTime;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Execution Time Tracker Class
 *
 * This class tracks execution time for commands, saves the data to a file,
 * and displays statistics about execution times.
 */
class TimeTracker
{
    /**
     * Path to the file where execution times are stored
     */
    private string $dataFile;

    /**
     * Output interface for console output
     */
    private OutputInterface $output;

    /**
     * Constructor
     */
    public function __construct(OutputInterface $output, string $dataFile = null)
    {
        $this->output = $output;
        $this->dataFile = $dataFile ?? __DIR__.'/../../data/execution_times.csv';

        // Ensure the data directory exists
        $dataDir = dirname($this->dataFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        // Create the data file if it doesn't exist
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, "date,command,execution_time_seconds\n");
        }
    }

    /**
     * Track execution time for a command
     *
     * @param string $commandName The name of the command
     * @param callable $callback The command to execute
     * @return mixed The result of the command
     */
    public function trackExecutionTime(string $commandName, callable $callback)
    {
        // Record the start time
        $start = new DateTime();

        // Execute the command
        $result = $callback();

        // Record the end time
        $end = new DateTime();

        // Calculate the time difference in seconds
        $diff = $end->diff($start);
        $seconds = $diff->s + ($diff->i * 60) + ($diff->h * 3600);

        // Save the execution time to the file
        $this->saveExecutionTime($commandName, $start, $seconds);

        // Display current execution time
        $this->displayCurrentExecutionTime($seconds);

        // Display statistics
        $this->displayStatistics($commandName);

        return $result;
    }

    /**
     * Display the current execution time
     *
     * @param float $seconds The execution time in seconds
     */
    private function displayCurrentExecutionTime(float $seconds): void
    {
        $this->output->writeln('');
        $this->output->writeln('Current Execution Time:');
        $currentTimeTable = new Table($this->output);
        $currentTimeTable->setHeaders(['Metric', 'Value']);
        $currentTimeTable->addRow(['Execution time', sprintf('%.2f seconds', $seconds)]);
        $currentTimeTable->render();
    }

    /**
     * Calculate statistics for execution times
     *
     * @param array<array<string, mixed>> $executionTimes Array of execution times
     * @return array<string, float> Statistics
     */
    private function calculateStatistics(array $executionTimes): array
    {
        $seconds = array_column($executionTimes, 'seconds');

        return [
            'best' => min($seconds),
            'worst' => max($seconds),
            'average' => array_sum($seconds) / count($seconds),
        ];
    }

    /**
     * Display statistics about execution times
     *
     * @param string $commandName The name of the command
     */
    private function displayStatistics(string $commandName): void
    {
        $this->output->writeln('');
        $this->output->writeln('Execution Time Statistics:');

        // Get all execution times for this command
        $executionTimes = $this->getExecutionTimes($commandName);

        if (empty($executionTimes)) {
            $this->output->writeln('No previous execution times found for this command.');

            return;
        }

        // Create a table for all time statistics
        $allTimeStats = $this->calculateStatistics($executionTimes);
        $this->output->writeln('All-time statistics:');
        $allTimeTable = new Table($this->output);
        $allTimeTable->setHeaders(['Metric', 'Value (seconds)']);
        $allTimeTable->addRow(['Best time', sprintf('%.2f', $allTimeStats['best'])]);
        $allTimeTable->addRow(['Worst time', sprintf('%.2f', $allTimeStats['worst'])]);
        $allTimeTable->addRow(['Average time', sprintf('%.2f', $allTimeStats['average'])]);
        $allTimeTable->render();

        // Calculate statistics for last 30 days
        $last30DaysDate = new DateTime();
        $last30DaysDate->sub(new DateInterval('P30D'));
        $last14DaysExecutionTimes = array_filter($executionTimes, function ($item) use ($last30DaysDate) {
            return $item['date'] >= $last30DaysDate;
        });

        if (!empty($last14DaysExecutionTimes)) {
            $last14DaysStats = $this->calculateStatistics($last14DaysExecutionTimes);
            $this->output->writeln('Last 30 days statistics:');
            $last30DaysTable = new Table($this->output);
            $last30DaysTable->setHeaders(['Metric', 'Value (seconds)']);
            $last30DaysTable->addRow(['Best time', sprintf('%.2f', $last14DaysStats['best'])]);
            $last30DaysTable->addRow(['Worst time', sprintf('%.2f', $last14DaysStats['worst'])]);
            $last30DaysTable->addRow(['Average time', sprintf('%.2f', $last14DaysStats['average'])]);
            $last30DaysTable->render();
        }

        // Calculate statistics for last 24 hours
        $last24HoursDate = new DateTime();
        $last24HoursDate->sub(new DateInterval('PT24H'));
        $last24HoursExecutionTimes = array_filter($executionTimes, function ($item) use ($last24HoursDate) {
            return $item['date'] >= $last24HoursDate;
        });

        if (!empty($last24HoursExecutionTimes)) {
            $last24HoursStats = $this->calculateStatistics($last24HoursExecutionTimes);
            $this->output->writeln('Last 24 hours statistics:');
            $last24HoursTable = new Table($this->output);
            $last24HoursTable->setHeaders(['Metric', 'Value (seconds)']);
            $last24HoursTable->addRow(['Best time', sprintf('%.2f', $last24HoursStats['best'])]);
            $last24HoursTable->addRow(['Worst time', sprintf('%.2f', $last24HoursStats['worst'])]);
            $last24HoursTable->addRow(['Average time', sprintf('%.2f', $last24HoursStats['average'])]);
            $last24HoursTable->render();
        }
    }

    /**
     * Get all execution times for a command
     *
     * @param string $commandName The name of the command
     * @return array<array<string, mixed>> Array of execution times
     */
    private function getExecutionTimes(string $commandName): array
    {
        $executionTimes = [];

        if (!file_exists($this->dataFile)) {
            return $executionTimes;
        }

        $file = fopen($this->dataFile, 'r');

        // Skip the header row
        fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            if (count($row) >= 3 && $row[1] === $commandName) {
                $executionTimes[] = [
                    'date' => new DateTime($row[0]),
                    'seconds' => (float) $row[2],
                ];
            }
        }

        fclose($file);

        return $executionTimes;
    }

    /**
     * Save execution time to the file
     *
     * @param string $commandName The name of the command
     * @param DateTime $date The date and time of execution
     * @param float $seconds The execution time in seconds
     */
    private function saveExecutionTime(string $commandName, DateTime $date, float $seconds): void
    {
        $data = sprintf("%s,%s,%.2f\n", $date->format('Y-m-d H:i:s'), $commandName, $seconds);
        file_put_contents($this->dataFile, $data, FILE_APPEND);
    }
}
