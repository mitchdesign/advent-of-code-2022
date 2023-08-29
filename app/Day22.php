<?php

namespace App;

use Illuminate\Support\Collection;

class Day22 extends Day
{
    public static string $title = 'Monkey Map';

    private array $map;

    private Collection $directions;

    private int $row;
    private int $col;
    private int $dir;

    private int $maxRow;
    private int $maxCol;

    private bool $cube = false;

    private const RIGHT = 0;
    private const DOWN = 1;
    private const LEFT = 2;
    private const UP = 3;

    public function __construct()
    {
        parent::__construct();

//        $this->input =
//            '        ...#
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

        [$map, $directions] = explode("\n\n", $this->input);

        $this->map = $this->parseMap($map);
        $this->directions = $this->parseDirections($directions);

        [$this->row, $this->col] = $this->getStart();
        $this->dir = self::RIGHT;
    }

    public function puzzle1(): int
    {
        foreach ($this->directions as $action) {
            $action();
        }

        return 1000 * ($this->row + 1) + 4 * ($this->col + 1) + $this->dir;
    }

    public function puzzle2(): int
    {
        $this->cube = true;

        foreach ($this->directions as $action) {
            $action();
        }

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

    private function parseDirections(string $directions): Collection
    {
        return str($directions)
            ->replace(['L', 'R'], [' L ', ' R '])
            ->trim()
            ->explode(' ')
            ->map(fn ($item) => is_numeric($item) ? fn () => $this->move($item) : fn () => $this->turn($item));
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
            // this was a step
        }

//        dump("{$this->row}, {$this->col}, {$this->dir}");
    }

    // take a step in $dir direction. return true if ok, or false if the step was blocked due to an obstacle.
    private function step(): bool
    {
        $nextRow = $this->row;
        $nextCol = $this->col;
        $nextDir = $this->dir;

        $delta = match ($this->dir) {
            self::RIGHT => [0, 1],
            self::LEFT => [0, -1],
            self::UP => [-1, 0],
            self::DOWN => [1, 0],
        };

        $faceBefore = $this->getFace($nextRow, $nextCol);

        $nextRow += $delta[0];
        $nextCol += $delta[1];

        if ($faceBefore != $this->getFace($nextRow, $nextCol)) {
            [$nextRow, $nextCol, $nextDir] = $this->getRowColAfterFaceTransition($faceBefore, $this->dir, $nextRow, $nextCol);
        }

        if ($this->map[$nextRow][$nextCol] === '#') {
            return false;
        }

        $this->row = $nextRow;
        $this->col = $nextCol;
        $this->dir = $nextDir;

        return true;
    }

    private function getFace(int $row, int $col): string
    {
        $row = (int) floor ($row / 50);
        $col = (int) floor ($col / 50);

//        +---+---+---+
//        |   | A | B |
//        +---+---+---+
//        |   | C |   |
//        +---+---+---+
//        | D | E |   |
//        +___+___+___+
//        | F |   |   |
//        +---+---+---+

        return match (10 * $row + $col) {
            1 => 'A',
            2 => 'B',
            11 => 'C',
            20 => 'D',
            21 => 'E',
            30 => 'F',
            default => '',
        };
    }

    private function getRowColAfterFaceTransition(string $faceBefore, int $dir, int $row, int $col): array
    {
        if (! $this->cube) {
            switch ($faceBefore . $dir) {
                case 'A' . self::LEFT:
                case 'D' . self::LEFT:
                    $col += 100;
                    break;
                case 'B' . self::RIGHT:
                case 'E' . self::RIGHT:
                    $col -= 100;
                    break;
                case 'C' . self::LEFT:
                case 'F' . self::LEFT:
                    $col += 50;
                    break;
                case 'C' . self::RIGHT:
                case 'F' . self::RIGHT:
                    $col -= 50;
                    break;
                case 'A' . self::UP:
                    $row += 150;
                    break;
                case 'B' . self::UP:
                    $row += 50;
                    break;
                case 'D' . self::UP:
                    $row += 100;
                    break;
                case 'E' . self::DOWN:
                    $row -= 150;
                    break;
                case 'B' . self::DOWN:
                    $row -= 50;
                    break;
                case 'F' . self::DOWN:
                    $row -= 100;
                    break;
            }

            return [$row, $col, $dir];
        }
    }
}
