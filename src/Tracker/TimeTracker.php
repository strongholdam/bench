<?php

namespace Stronghold\Bench\Tracker;

use DateInterval;
use DateTime;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

/**
 * Execution Time Tracker Class
 *
 * This class tracks execution time for commands, saves the data to a file,
 * and displays statistics about execution times.
 */
class TimeTracker
{
    private string $dataFile;

    private OutputInterface $output;

    public function __construct(OutputInterface $output, string $dataFile = null)
    {
        $this->output = $output;
        $this->dataFile = $dataFile ?? __DIR__.'/../../data/execution_times.csv';

        $dataDir = dirname($this->dataFile);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, "date,command,execution_time_seconds\n");
        }
    }

    public function trackExecutionTime(string $commandName, callable $callback): mixed
    {
        $start = new DateTime();
        $result = $callback();
        $end = new DateTime();
        $diff = $end->diff($start);
        $seconds = $diff->s + ($diff->i * 60) + ($diff->h * 3600);
        $this->saveExecutionTime($commandName, $start, $seconds);
        $this->displayCurrentExecutionTime($seconds);
        $this->displayStatistics($commandName);

        return $result;
    }

    protected function calculateStatisticsForAllTimes(array $executionTimes): void
    {
        if (empty($executionTimes)) {
            return;
        }
        $statistics = $this->calculateStatistics($executionTimes);
        $this->output->writeln(['', 'All-time statistics:']);
        $this->table($statistics);
    }

    protected function calculateStatisticsForLast30Days(array $executionTimes): void
    {
        $last30DaysDate = new DateTime();
        $last30DaysDate->sub(new DateInterval('P30D'));
        $executionTimes = array_filter($executionTimes, function ($item) use ($last30DaysDate) {
            return $item['date'] >= $last30DaysDate;
        });

        if (empty($executionTimes)) {
            return;
        }
        $statistics = $this->calculateStatistics($executionTimes);
        $this->output->writeln(['', 'Last 30 days statistics:']);
        $this->table($statistics);
    }

    private function calculateStatisticsForLast24Hours(array $executionTimes): void
    {
        $last24HoursDate = new DateTime();
        $last24HoursDate->sub(new DateInterval('PT24H'));
        $executionTimes = array_filter($executionTimes, function ($item) use ($last24HoursDate) {
            return $item['date'] >= $last24HoursDate;
        });

        if (empty($executionTimes)) {
            return;
        }
        $statistics = $this->calculateStatistics($executionTimes);
        $this->output->writeln(['', 'Last 24 hours statistics:']);
        $this->table($statistics);
    }

    private function table($statistics): void
    {
        $last24HoursTable = new Table($this->output);
        $last24HoursTable->setHeaders(['Metric', 'Value (seconds)']);
        $last24HoursTable->addRows([
            ['Best time', sprintf('%.2f', $statistics['best'])],
            ['Worst time', sprintf('%.2f', $statistics['worst'])],
            ['Average time', sprintf('%.2f', $statistics['average'])],
            ['Count', number_format($statistics['count'])],
        ]);
        $last24HoursTable->render();
    }

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
            'count' => count($executionTimes),
        ];
    }

    private function displayStatistics(string $commandName): void
    {
        // Get all execution times for this command
        $executionTimes = $this->getExecutionTimes($commandName);
        if (empty($executionTimes)) {
            $this->output->writeln('No previous execution times found for this command.');

            return;
        }
        $this->calculateStatisticsForLast24Hours($executionTimes);
        $this->calculateStatisticsForLast30Days($executionTimes);
        $this->calculateStatisticsForAllTimes($executionTimes);
    }

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
