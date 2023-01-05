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
    }

    public function initCave(bool $addFloor = false): void
    {
        // prep the input into a collection of rock layers
        $rocks = str($this->input)
            ->trim()
            ->explode("\n")
            ->map(fn ($layer) => str($layer)->explode(' -> ')->map(fn ($point) => explode(',', $point)));

        $x = $rocks->flatten(1)->pluck(0)->add($this->entryPoint[0]);
        $y = $rocks->flatten(1)->pluck(1)->add($this->entryPoint[1]);

        $left = $x->min();
        $right = $x->max();
        $top = $y->min();
        $bottom = $y->max();

        // alter the map with a floor and make it wider
        // big-picture it will look like a pyramid so we make add one height to the left and right
        if ($addFloor) {
            $height = $bottom - $top;
            $left -= $height;
            $right += $height;
            $bottom += 2;

            // add the rock layer to the definition
            $rocks->add(collect([[$left, $bottom], [$right, $bottom]]));
        }

        $xArray = array_fill_keys(range($left, $right), self::AIR);
        $this->map = array_fill_keys(range($top, $bottom), $xArray);

        foreach ($rocks as $layer) {
            for ($i = 1; $i < $layer->count(); $i++) {
                foreach (range($layer->get($i)[0], $layer->get($i - 1)[0]) as $x) {
                    foreach (range($layer->get($i)[1], $layer->get($i - 1)[1]) as $y) {
                        $this->map[$y][$x] = self::ROCK;
                    }
                }
            }
        }

        $this->visualize();
    }

    public function result1(): int
    {
        $this->initCave();
        $this->go();

        return $this->sandCount;
    }

    public function result2(): int
    {
        $this->initCave(true);
        $this->go();

        return $this->sandCount;
    }

    public function go(): void
    {
        while ([$x, $y] = $this->dropSand()) {
            $this->map[$y][$x] = self::SAND;
            if ($this->sandCount % $this->visualisationInterval === 0) {
                $this->visualize();
            }
        }
        $this->visualize();
    }

    public function dropSand(): ?array
    {
        $position = $this->entryPoint;

        try {
            while ($this->map[$this->entryPoint[1]][$this->entryPoint[0]] === self::AIR) {  // stop the loop when entry has been blocked
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

        return null;
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

        echo '<pre style="font-size: .25rem;">' . str_replace(
            [self::AIR, self::SAND, self::ROCK],
            ['.', 'o', '#'],
    join("\n", array_map(fn ($row) => join('', $row), $map)) . '</pre>'
        );
    }
}
