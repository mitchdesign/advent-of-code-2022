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

//        $this->input = '2,2,2
//1,2,2
//3,2,2
//2,1,2
//2,3,2
//2,2,1
//2,2,3
//2,2,4
//2,2,6
//1,2,5
//3,2,5
//2,1,5
//2,3,5';

        $this->input = trim($this->input);
        $this->scanInput();
    }

    public function puzzle1(): int
    {
        // puzzle 1 : check for the bounderies between rock and anything else
        return $this->countBoundaries(self::ROCK);
    }

    public function puzzle2(): int
    {
        // puzzle 2 : first find and mark all the free-air blocks.
        // then check for the boundaries between free air and anything else
        $this->traceFreeAir();

        return $this->countBoundaries(self::FREE_AIR);
    }

    protected function countBoundaries(int $boundaryType): int
    {
        $count = 0;

        foreach (['x', 'y', 'z'] as $axis) {
            [$firstAxis, $secondAxis] = array_values(array_diff(['x', 'y', 'z'], [$axis]));

            foreach (range(...$this->extremes[$firstAxis]) as $firstCount) {
                ${$firstAxis} = $firstCount;

                foreach (range(...$this->extremes[$secondAxis]) as $secondCount) {
                    ${$secondAxis} = $secondCount;

                    // start from min, with previous = FREE_AIR
                    ${$axis} = $this->extremes[$axis][0];
                    $previous = self::FREE_AIR;

                    while (${$axis} <= $this->extremes[$axis][1] + 1)
                    {
                        $current = ${$axis} === $this->extremes[$axis][1] + 1
                            ? self::FREE_AIR
                            : $this->droplets[$x][$y][$z];

                        if (($previous === $boundaryType) !== ($current === $boundaryType)) {
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
        $rocks = str($this->input)
            ->trim()
            ->split('/\n/')
            ->map(fn ($line) => str($line)->explode(',')
                ->map(fn ($val) => (int) $val)->toArray()
            )->toArray();

        $this->extremes = [
            'x' => [min($v = array_column($rocks, 0)), max($v)],
            'y' => [min($v = array_column($rocks, 1)), max($v)],
            'z' => [min($v = array_column($rocks, 2)), max($v)],
        ];

        $this->droplets = array_fill_keys(range(...$this->extremes['x']), array_fill_keys(range(...$this->extremes['y']), array_fill_keys(range(...$this->extremes['z']), self::CONTAINED_AIR)));

        foreach ($rocks as $rock) {
            [$x, $y, $z] = $rock;
            $this->droplets[$x][$y][$z] = self::ROCK;
        }
    }

    protected function traceFreeAir(): void
    {
        $this->setTraceStart();

        while ([$x, $y, $z] = $this->getNextToScan()) {

            // if this is a rock, we are done finding adjacent free air.
            if ($this->droplets[$x][$y][$z] === self::ROCK) {
                continue;
            }

            // no rock. this node is added to free air
            $this->droplets[$x][$y][$z] = self::FREE_AIR;

            // and we can scan the adjacent nodes
            foreach ($this->getNeighbors($x, $y, $z) as $neighbor) {
                $this->addToScanIfNotAdded($neighbor);
            }
        }
    }

    protected function getNextToScan(): ?array
    {
        $next = array_shift($this->toScan);

        if (is_null($next)) {
            return null;
        }

        $this->scanned[] = $next;

        [$x, $y, $z] = explode(',', $next);

        return [(int) $x, (int) $y, (int) $z];
    }

    protected function getNeighbors(int $x, int $y, int $z): array
    {
        return array_filter([
            [$x - 1, $y, $z],
            [$x + 1, $y, $z],
            [$x, $y - 1, $z],
            [$x, $y + 1, $z],
            [$x, $y, $z - 1],
            [$x, $y, $z + 1],
        ], fn ($node) => isset($this->droplets[$node[0]][$node[1]][$node[2]]));
    }

    protected function addToScanIfNotAdded(array $node): void
    {
        $string = join(',', $node);

        if (! in_array($string, $this->toScan) && ! in_array($string, $this->scanned)) {
            $this->toScan[] = $string;
        }
    }

    protected function setTraceStart(): void
    {
        foreach (range(...$this->extremes['x']) as $x) {
            foreach (range(...$this->extremes['y']) as $y) {
                $this->addToScanIfNotAdded([$x, $y, $this->extremes['z'][0]]);
                $this->addToScanIfNotAdded([$x, $y, $this->extremes['z'][1]]);
            }
        }

        foreach (range(...$this->extremes['x']) as $x) {
            foreach (range(...$this->extremes['z']) as $z) {
                $this->addToScanIfNotAdded([$x, $this->extremes['y'][0], $z]);
                $this->addToScanIfNotAdded([$x, $this->extremes['y'][1], $z]);
            }
        }

        foreach (range(...$this->extremes['y']) as $y) {
            foreach (range(...$this->extremes['z']) as $z) {
                $this->addToScanIfNotAdded([$this->extremes['x'][0], $y, $z]);
                $this->addToScanIfNotAdded([$this->extremes['x'][1], $y, $z]);
            }
        }
    }
}
