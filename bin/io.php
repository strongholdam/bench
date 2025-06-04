<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/config/config.php';

use Stronghold\Bench\IO;

/**
 * I/O Benchmark Controller
 *
 * This script uses the IO class to orchestrate the I/O benchmark by:
 * 1. Creating a temporary file
 * 2. Running the write test
 * 3. Running the read test (multiple iterations)
 * 4. Displaying a performance summary
 */

// Create a new IO benchmark instance
$ioBenchmark = new IO();

// Run the benchmark
$results = $ioBenchmark->run();

// Results are already displayed in the run method
