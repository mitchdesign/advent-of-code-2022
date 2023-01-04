<?php

namespace App;

class Day14 extends Day
{
    public static string $title = 'Regolith Reservoir';

    public array $entryPoint = [500, 0];

    public array $map = [];

    public const ROCK = 2;
    public const SAND = 1;
    public const AIR = 0;

    public int $sandCount = 0;

    public int $visualisationInterval = 50;

    public function __construct()
    {
        parent::__construct();

//        $this->input = '498,4 -> 498,6 -> 496,6
//503,4 -> 502,4 -> 502,9 -> 494,9';

        // prep the input into a collection of rock layers
        $rocks = str($this->input)
            ->trim()
            ->explode("\n")
            ->map(fn ($layer) => str($layer)->explode(' -> ')->map(fn ($point) => explode(',', $point)));

        $x = $rocks->flatten(1)->pluck(0)->add($this->entryPoint[0]);
        $y = $rocks->flatten(1)->pluck(1)->add($this->entryPoint[1]);

        $xArray = array_fill_keys(range($x->min(), $x->max()), self::AIR);
        $this->map = array_fill_keys(range($y->min(), $y->max()), $xArray);

        foreach ($rocks as $layer) {
            for ($i = 1; $i < $layer->count(); $i++) {
                foreach (range($layer->get($i)[0], $layer->get($i - 1)[0]) as $x) {
                    foreach (range($layer->get($i)[1], $layer->get($i - 1)[1]) as $y) {
                        $this->map[$y][$x] = self::ROCK;
                    }
                }
            }
        }

        // $this->visualize();  // uncomment to show initial rock formation
    }

    public function result1(): int
    {
        while ([$x, $y] = $this->dropSand()) {
            $this->map[$y][$x] = self::SAND;
            if ($this->sandCount % $this->visualisationInterval === 0) {
                $this->visualize();
            }
        }

        return $this->sandCount;
    }

    public function result2(): int
    {
        return 1;
    }

    public function dropSand(): ?array
    {
        $position = $this->entryPoint;

        try {
            while (true) {
                $center = [$position[0], $position[1] + 1];
                if ($this->getPosition($center) === self::AIR) {
                    $position = $center;
                    continue;
                }

                $left = [$position[0] - 1, $position[1] + 1];
                if ($this->getPosition($left) === self::AIR) {
                    $position = $left;
                    continue;
                }

                $right = [$position[0] + 1, $position[1] + 1];
                if ($this->getPosition($right) === self::AIR) {
                    $position = $right;
                    continue;
                }
                // we couldn't move down, down-left or down-right.
                // this grain of sand settles.
                $this->sandCount++;

                return $position;
            }
        } catch (\RuntimeException $e) {
            return null;
        }
    }

    public function getPosition(array $position): int
    {
        if (! isset($this->map[$position[1]][$position[0]])) {
            throw new \RuntimeException('Out of bounds');
        }

        return $this->map[$position[1]][$position[0]];
    }

    public function visualize(): void
    {
        $map = $this->map;
        $map[0][500] = 'V';

        echo '<pre>' . str_replace(
            [self::AIR, self::SAND, self::ROCK],
            ['.', 'o', '#'],
    join("\n", array_map(fn ($row) => join('', $row), $map)) . '</pre>'
        );
    }
}
