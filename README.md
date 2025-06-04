# Server Benchmark Tool

A simple yet effective benchmarking suite for measuring server performance. This tool was created to help identify performance degradation in Virtual Private Servers (VPS), especially when other VPS instances on the same hypervisor are consuming excessive resources.

## Overview

When servers start experiencing slowdowns without clear causes, these benchmarks can help establish baseline performance metrics and detect degradation over time. The suite includes:

- **CPU Benchmark**: Measures computational performance by calculating prime numbers
- **I/O Benchmark**: Measures disk read/write speeds

## Requirements

- PHP 7.0 or higher
- Write permissions to the system's temporary directory (for I/O benchmark)

## Installation

Clone this repository to your server:

```bash
git clone https://github.com/yourusername/bench.git
cd bench
```

## Usage

### CPU Benchmark

Run the CPU benchmark to measure computational performance:

```bash
php src/cpu.php
```

This script calculates prime numbers up to 100,000 and measures the time taken. Longer execution times indicate reduced CPU performance.

### I/O Benchmark

Run the I/O benchmark to measure disk read/write performance:

```bash
php src/io.php
```

This script creates a temporary file (default 5GB), performs write and read operations, and measures the speeds. Lower MB/s values indicate reduced I/O performance.

## Interpreting Results

- **CPU Benchmark**: Record the execution time. Compare this with previous runs on the same server. Significant increases in execution time suggest CPU performance degradation.
- **I/O Benchmark**: Record the read/write speeds. Compare with previous runs. Significant decreases in MB/s suggest I/O performance degradation.

## Best Practices

- Run benchmarks during low-traffic periods to establish a baseline
- Run benchmarks periodically to track performance over time
- Compare results only between identical server configurations
- Consider running multiple times and averaging the results for more accurate measurements

## License

See the [LICENSE](LICENSE) file for details.
