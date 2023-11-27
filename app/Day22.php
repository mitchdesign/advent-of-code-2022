<?php

namespace App;

use App\Models\Day22\Map2D;
use App\Models\Day22\Position2D;

class Day22 extends Day
{
    public static string $title = 'Monkey Map';

    private const RIGHT = 0;
    private const DOWN = 1;
    private const LEFT = 2;
    private const UP = 3;

    private array $dirVectors = [
        // [ row, col ] deltas
        self::RIGHT => [0, 1],
        self::DOWN => [1, 0],
        self::LEFT => [0, -1],
        self::UP => [-1, 0],
    ];

    private array $map;

    private int $maxRow;
    private int $maxCol;
    private array $directions;
    private array $jumpMap;

    private int $row = 0;
    private int $col = 0;
    private int $dir = 0;

    private bool $printMap = false;
    private bool $printMoves = false;
    private array $visualizeMap = [];

    public function __construct()
    {
        parent::__construct();

//        $this->input =
//'        ...#
//        .#..
//        #...
//        ....
//...#.......#
//........#...
//..#....#....
//..........#.
//        ...#....
//        .....#..
//        .#......
//        ......#.
//
//10R5L5R10L4R5L5';

        [$input, $directions] = explode("\n\n", $this->input);

        $this->map = $this->parseMap($input);

        $this->visualizeMap = $this->map;

        $this->directions = $this->parseDirections($directions);

        [$this->row, $this->col] = $this->getStart();
        $this->dir = self::RIGHT;
    }

    public function puzzle1(): int
    {
        $this->jumpMap = $this->getJumpMapFlat();

        foreach ($this->directions as $dir) {
            $dir();
        }

        return $this->getScore();
    }

    public function puzzle2(): int
    {
        $this->jumpMap = $this->getJumpMap3D();

        foreach ($this->directions as $dir) {
            $dir();
        }

        return $this->getScore();
    }

    private function getScore(): int
    {
        return 1000 * ($this->row + 1) + 4 * ($this->col + 1) + $this->dir;
    }

    private function parseMap(string $map): array
    {
        $map = str($map)
            ->explode("\n");

        $this->maxRow = $map->keys()->last();
        $maxLength = $map->map(fn ($row) => strlen($row))->max();
        $this->maxCol = $maxLength - 1;

        return $map->map(fn ($line) => str($line)->split(1)->map(fn ($char) => $char == ' ' ? null : $char)->pad($maxLength, null))
            ->toArray();
    }

    private function parseDirections(string $directions): array
    {
        return str($directions)
            ->replace(['L', 'R'], [' L ', ' R '])
            ->trim()
            ->explode(' ')
            ->map(fn ($item) => is_numeric($item) ? fn () => $this->move($item) : fn () => $this->turn($item))
            ->toArray();
    }

    private function getStart(): array
    {
        $row = 0;
        $col = collect($this->map[0])->takeUntil(fn ($val) => ! is_null($val))->count();

        return [$row, $col];
    }

    private function turn(string $direction): void
    {
        if ($direction == 'L') {
            if (--$this->dir == -1) {
                $this->dir = 3;
            }
            return;
        }

        if (++$this->dir == 4) {
            $this->dir = 0;
        }
    }

    private function move(int $steps): void
    {
        $step = 0;

        while (++$step <= $steps && $this->step()) {
            $this->visualizeMap[$this->row][$this->col] = [
                self::RIGHT => '>',
                self::DOWN => 'V',
                self::LEFT => '<',
                self::UP => '^',
            ][$this->dir];
            // this was a step
        }
        if ($this->printMoves) {
            $this->visualize();
        }
    }

    // take a step in $dir direction. return true if ok, or false if the step was blocked due to an obstacle.
    private function step(): bool
    {
        if (! ([$nextRow, $nextCol, $nextDir] = $this->getJump())) {
            $delta = $this->dirVectors[$this->dir];
            $nextRow = $this->row + $delta[0];
            $nextCol = $this->col + $delta[1];
            $nextDir = $this->dir;
        }

        if ($this->map[$nextRow][$nextCol] === '#') {
            return false;
        }

        $this->row = $nextRow;
        $this->col = $nextCol;
        $this->dir = $nextDir;

        return true;
    }

    protected function getJump(): ?array
    {
        return $this->jumpMap[$this->row][$this->col][$this->dir] ?? null;
    }

    protected function getJumpMapFlat(): array
    {
        $transposedMap = array_map(null, ...$this->map);

        $jumpMap = [];

        for ($row = 0; $row <= $this->maxRow; $row++) {
            $left = collect($this->map[$row])->takeWhile(fn ($val) => is_null($val))->count();
            $right = $this->maxCol - collect($this->map[$row])->reverse()->takeWhile(fn ($val) => is_null($val))->count();
            $jumpMap[$row][$left][(string) self::LEFT] = [$row, $right, self::LEFT];
            $jumpMap[$row][$right][(string) self::RIGHT] = [$row, $left, self::RIGHT];
        }

        for ($col = 0; $col <= $this->maxCol; $col++) {
            $top = collect($transposedMap[$col])->takeWhile(fn ($val) => is_null($val))->count();
            $bottom = $this->maxRow - collect($transposedMap[$col])->reverse()->takeWhile(fn ($val) => is_null($val))->count();
            $jumpMap[$top][$col][(string) self::UP] = [$bottom, $col, self::UP];
            $jumpMap[$bottom][$col][(string) self::DOWN] = [$top, $col, self::DOWN];
        }

        return $jumpMap;
    }

    protected function getJumpMap3D(): array
    {
        // Find the inner corners of the folding pattern.
        // Those are the start for tracing which edges touch.
        // We follow each one until the two edges we follow,
        // both turn at the same time which means they are no longer touching.

        $starts = [];

        for ($row = 0; $row <= $this->maxRow - 1; $row++) {
            for ($col = 0; $col <= $this->maxCol - 1; $col++) {
                if (collect([
                    $this->map[$row][$col],
                    $this->map[$row][$col + 1],
                    $this->map[$row + 1][$col],
                    $this->map[$row + 1][$col + 1]
                ])->filter(fn ($val) => is_null($val))->count() === 1) {
                    $starts[] = [$row + 0.5, $col + 0.5];
                }
            }
        }

        $jumpMap = [];

        $x = 64;

        foreach ($starts as $start) {
            $visited = [$start];
            $prevDir1 = $prevDir2 = null;

            [$edge1, $edge2] = $this->findNextNodes($start, $visited);

            while ($edge1 && $edge2 && (is_null($prevDir1) || $prevDir1 == $edge1['jump'][2] || $prevDir2 == $edge2['jump'][2])) {
                if ($this->printMap) {
                    $x++;
                    if ($x > 65 + 63) {
                        $x = 65;
                    }
                    $this->visualizeMap[$edge1['jump'][0]][$edge1['jump'][1]] =
                    $this->visualizeMap[$edge2['jump'][0]][$edge2['jump'][1]] = chr($x);
                }
                $j1 = $edge1['jump'];
                $j2 = $edge2['jump'];

                $prevDir1 = $j1[2];
                $prevDir2 = $j2[2];

                $jumpMap[$j1[0]][$j1[1]][(string)$j1[2]] = [$j2[0], $j2[1], self::reverseDir($j2[2])];
                $jumpMap[$j2[0]][$j2[1]][(string)$j2[2]] = [$j1[0], $j1[1], self::reverseDir($j1[2])];

                [$edge1] = $this->findNextNodes($edge1['node'], $visited);
                [$edge2] = $this->findNextNodes($edge2['node'], $visited);

                if ($this->printMap) {
                    $this->visualize();
                }
            }
        }

        if ($this->printMap) {
            $this->visualize();
        }

        return $jumpMap;
    }

    protected function findNextNodes(array &$node, array &$visited): array
    {
        // Going from a known node, find adjacent nodes that have not yet been stored
        $nodes = [];

        foreach ($this->dirVectors as $vector) {
            $adjacentNode = [
                $node[0] + $vector[0],
                $node[1] + $vector[1],
            ];

            if (in_array($adjacentNode, $visited)) {
                continue;
            }

            if ($vector[0] === 0) { // row delta = 0. this is left or right
                $col = ($node[1] + ($vector[1] / 2));
                $fields = [
                    [$node[0] - 0.5, $col],
                    [$node[0] + 0.5, $col],
                ];
                $dir = is_null($this->map[$fields[0][0]][$fields[0][1]] ?? null)
                    ? self::UP
                    : self::DOWN;
            } else { // col delta = 0. this is up or down.
                $row = ($node[0] + ($vector[0] / 2));
                $fields = [
                    [$row, $node[1] - 0.5],
                    [$row, $node[1] + 0.5],
                ];
                $dir = is_null($this->map[$fields[0][0]][$fields[0][1]] ?? null)
                    ? self::LEFT
                    : self::RIGHT;
            }

            $filtered = collect($fields)->filter(fn($coords) => $this->map[$coords[0]][$coords[1]] ?? null);

            if ($filtered->count() === 1) { // 1 left. so we are on an edge of null and not null fields
                $field = $filtered->first();
                $visited[] = $adjacentNode;
                $nodes[] = [
                    'node' => $adjacentNode,
                    'jump' => [$field[0], $field[1], $dir],
                ];
            }
        }

        return $nodes ?: [null];
    }

    protected function reverseDir(int $dir): int
    {
        return match ($dir) {
            self::UP => self::DOWN,
            self::DOWN => self::UP,
            self::LEFT => self::RIGHT,
            self::RIGHT => self::LEFT,
        };
    }

    protected function visualize(): void
    {
        echo( '<pre>' . join("\n", array_map(fn ($r) => join('', array_map(fn ($s) => $s ?? ' ', $r)), $this->visualizeMap)) . '</pre><br><br>');
    }
}
