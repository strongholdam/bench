<?php

/**
 * Configuration file for benchmarks
 */

// I/O Benchmark Configuration
// Size of the test file in MB
const FILE_SIZE_MB = 15000;

// Size of each read/write chunk in bytes
const CHUNK_SIZE = 8192;

// Number of times to read the file
const READ_ITERATIONS = 3;

// CPU Benchmark Configuration
// Maximum number of integers to check for primality
const MAX_NUMBERS_TO_CALCULATE = 120000;
