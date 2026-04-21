<?php

namespace Stronghold\Bench\CPU;

use Symfony\Component\Console\Output\OutputInterface;

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
        $lastDot = microtime(true);
        while ($this->current < MAX_NUMBERS_TO_CALCULATE) {
            if ($this->isPrime($this->current)) {
                $this->primes[] = $this->current;
            }
            if (microtime(true) - $lastDot >= 4.5) {
                $this->output->write('.');
                $lastDot = microtime(true);
            }
            ++$this->current;
        }

        $this->output->writeln('');

        // Display the benchmark results
        $this->output->writeln(sprintf('Found %d number primes.', count($this->primes)));
        $this->output->writeln(sprintf('Last number prime found was %d.', end($this->primes)));

        return [
            'primes_count' => count($this->primes),
            'last_prime' => end($this->primes),
        ];
    }
}
