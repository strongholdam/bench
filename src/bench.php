<?php

const MAX_NUMBERS_TO_CALCULATE = 100000;

$start = new \DateTime();

function isPrime($num): bool
{
    $cont = 0;
    for ($i = 1; $i <= $num; ++$i) {
        if (0 == $num % $i) {
            $cont = $cont + 1;
        }
    }
    if (2 == $cont) {
        return true;
    }

    return false;
}

$primes = [];
$current = 1;
while ($current < MAX_NUMBERS_TO_CALCULATE) {
    if (isPrime($current)) {
        $primes[] = $current;
    }
    if ($current % 100 == 0) {
        echo '.';
    }
    ++$current;
}

$end = new \DateTime();
$diff = $end->diff($start);
echo PHP_EOL.PHP_EOL.PHP_EOL;
echo sprintf('Found %d number primes.', count($primes)).PHP_EOL;
echo sprintf('Last number prime found was %d.', end($primes)).PHP_EOL;
echo sprintf('This calculation take %02d:%02d:%02d.', $diff->h, $diff->i, $diff->s).PHP_EOL;

