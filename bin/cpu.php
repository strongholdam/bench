<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/config/config.php';

use Stronghold\Bench\CPU;

// Create a new CPU benchmark instance
$cpuBenchmark = new CPU();

// Run the benchmark
$results = $cpuBenchmark->run();

// Results are already displayed in the run method