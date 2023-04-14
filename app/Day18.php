<?php

namespace App;

class Day18 extends Day
{
    public static string $title = 'Boiling Boulders';

    const ROCK = 1;
    const FREE_AIR = 2;
    const CONTAINED_AIR = 3;

    protected array $droplets;
    protected array $extremes = [];
    protected array $toScan = [];
    protected array $scanned = [];

    public function __construct()
    {
        parent::__construct();

        $this->input = '2,2,2
1,2,2
3,2,2
2,1,2
2,3,2
2,2,1
2,2,3
2,2,4
2,2,6
1,2,5
3,2,5
2,1,5
2,3,5';

        $this->input = trim($this->input);
        $this->scanInput();
    }

    public function puzzle1(): int
    {
        // puzzle 1 : check for the bounderies between rock and anything else
        return $this->countBoundaries(fn ($a, $b) => ($a === self::ROCK) !== ($b === self::ROCK));
    }

    public function puzzle2(): int
    {
        // puzzle 2 : first find and mark all the free-air blocks.
        // then check for the boundaries between free air and anything else
        $this->traceFreeAir();

        return $this->countBoundaries(fn ($a, $b) => ($a === self::FREE_AIR) !== ($b === self::FREE_AIR));
    }

    protected function countBoundaries(callable $comparator): int
    {
        $count = 0;

        foreach (['x', 'y', 'z'] as $axis) {
            [$firstAxis, $secondAxis] = array_values(array_diff(['x', 'y', 'z'], [$axis]));

            foreach (range($this->extremes[$firstAxis]['min'], $this->extremes[$firstAxis]['max']) as $firstCount) {
                ${$firstAxis} = $firstCount;

                foreach (range($this->extremes[$secondAxis]['min'], $this->extremes[$secondAxis]['max']) as $secondCount) {
                    ${$secondAxis} = $secondCount;

                    // start from min, with previous = FREE_AIR
                    ${$axis} = $this->extremes[$axis]['min'];
                    $previous = self::FREE_AIR;

                    while (${$axis} <= $this->extremes[$axis]['max'] + 1)
                    {
                        $current = ${$axis} === $this->extremes[$axis]['max'] + 1
                            ? self::FREE_AIR
                            : $this->droplets[$x][$y][$z];

                        if ($comparator($previous, $current)) {
                            $count++;
                        }

                        $previous = $current;
                        ${$axis}++;
                    }
                }
            }
        }

        return $count;
    }

    protected function scanInput(): void
    {
        $extremesX = $extremesY = $extremesZ = [];

        foreach (explode("\n", $this->input) as $line) {
            [$x, $y, $z] = explode(',', trim($line));
            $this->droplets[(int) $x][(int) $y][(int) $z] = self::ROCK;
            $extremesX[] = (int) $x;
            $extremesY[] = (int) $y;
            $extremesZ[] = (int) $z;
        }

        $this->extremes = [
            'x' => [
                'min' => min($extremesX),
                'max' => max($extremesX),
            ],
            'y' => [
                'min' => min($extremesY),
                'max' => max($extremesY),
            ],
            'z' => [
                'min' => min($extremesZ),
                'max' => max($extremesZ),
            ],
        ];

        foreach (range($this->extremes['x']['min'], $this->extremes['x']['max']) as $x) {
            foreach (range($this->extremes['y']['min'], $this->extremes['y']['max']) as $y) {
                foreach (range($this->extremes['z']['min'], $this->extremes['z']['max']) as $z) {
                    $this->droplets[$x][$y][$z] ??= self::CONTAINED_AIR;
                }
            }
        }
    }

    protected function traceFreeAir(): void
    {

    }
}
