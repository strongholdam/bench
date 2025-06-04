<?php

namespace Stronghold\Bench;

use DateTime;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use const MAX_NUMBERS_TO_CALCULATE;

/**
 * CPU Benchmark Class
 * This class calculates prime numbers up to a specified limit to benchmark CPU performance
 * The execution time indicates the computational power of the server
 */
class CPU
{
    /**
     * Array to store the prime numbers found
     * @var array<int>
     */
    private array $primes = [];

    /**
     * Current number being checked
     */
    private int $current = 1;

    /**
     * Start time of the benchmark
     */
    private DateTime $start;

    /**
     * End time of the benchmark
     */
    private ?DateTime $end = null;

    /**
     * Output interface for console output
     */
    private OutputInterface $output;

    /**
     * Progress bar for displaying progress
     */
    private ?ProgressBar $progressBar = null;

    /**
     * Constructor
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Determines if a number is prime
     * A prime number has exactly two divisors: 1 and itself
     *
     * @param int $num The number to check
     * @return bool True if the number is prime, false otherwise
     */
    public function isPrime(int $num): bool
    {
        $cont = 0;
        // Count the number of divisors
        for ($i = 1; $i <= $num; ++$i) {
            if (0 == $num % $i) {
                $cont = $cont + 1;
            }
        }
        // A prime number has exactly 2 divisors
        if (2 == $cont) {
            return true;
        }

        return false;
    }

    /**
     * Run the CPU benchmark
     *
     * @return array<string, mixed> Results of the benchmark
     */
    public function run(): array
    {
        // Record the start time of the benchmark
        $this->start = new DateTime();

        // Initialize the progress bar
        $this->progressBar = new ProgressBar($this->output, MAX_NUMBERS_TO_CALCULATE);
        $this->progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
        $this->progressBar->start();

        // Check each number up to the maximum
        while ($this->current < MAX_NUMBERS_TO_CALCULATE) {
            // If the number is prime, add it to the array
            if ($this->isPrime($this->current)) {
                $this->primes[] = $this->current;
            }

            // Update the progress bar
            $this->progressBar->advance();

            ++$this->current;
        }

        // Finish the progress bar
        $this->progressBar->finish();
        $this->output->writeln('');

        // Record the end time of the benchmark
        $this->end = new DateTime();

        // Calculate the time difference
        $diff = $this->end->diff($this->start);

        // Add some spacing for better readability
        $this->output->writeln('');

        // Display the benchmark results
        $this->output->writeln(sprintf('Found %d number primes.', count($this->primes)));
        $this->output->writeln(sprintf('Last number prime found was %d.', end($this->primes)));
        $this->output->writeln(sprintf('This calculation take %02d:%02d:%02d.', $diff->h, $diff->i, $diff->s));

        return [
            'primes_count' => count($this->primes),
            'last_prime' => end($this->primes),
            'time_diff' => $diff,
        ];
    }
}
