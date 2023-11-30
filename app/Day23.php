<?php

namespace App;

use Illuminate\Support\Collection;

class Day23 extends Day
{
    public static string $title = 'Unstable Diffusion';

    public array $elves = [];

    private array $directions = [
        ['check' => [[-1, -1], [0, -1], [1, -1]], 'go' => [0, -1], 'name' => 'N'],   // N
        ['check' => [[-1, 1], [0, 1], [1, 1]], 'go' => [0, 1], 'name' => 'S'],   // S
        ['check' => [[-1, -1], [-1, 0], [-1, 1]], 'go' => [-1, 0], 'name' => 'W'],   // W
        ['check' => [[1, -1], [1, 0], [1, 1]], 'go' => [1, 0], 'name' => 'E'],   // E
    ];

    public function __construct()
    {
        parent::__construct();

//        $this->input =
//'....#..
//..###.#
//#...#.#
//.#...##
//#.###..
//##.#.##
//.#..#..';

        $lines = explode("\n", $this->input);
        $rows = count($lines);
        $cols = strlen($lines[0]);

        for ($x = 0; $x < $cols; $x++) {
            for ($y = 0; $y < $rows; $y++) {
                if (substr($lines[$y], $x, 1) === '#') {
                    $this->elves["{$x}.{$y}"] = [$x, $y];
                }
            }
        }
    }

    public function puzzle1(): int
    {
        for ($round = 1; $round <= 10; $round++) {
            $this->goRound();
        }

        [[$minx, $miny], [$maxx, $maxy]] = $this->getSquareDimensions();

        return (($maxx - $minx + 1) * ($maxy - $miny + 1)) - count($this->elves);
    }

    public function puzzle2(): int
    {
        $round = 0;

        do {
            $round++;
        } while ($this->goRound() > 0);

        return $round;
    }

    protected function goRound(): int
    {
        $suggestions = [];

        foreach ($this->elves as $xy => [$x, $y]) {
            $possibleMoves = array_filter($this->directions, function ($dir) use ($xy, $x, $y) {
                foreach ($dir['check'] as [$dx, $dy]) {
                    $key = ($x + $dx) . '.' . ($y + $dy);
                    if (isset($this->elves[$key]) && $key !== $xy) {
                        return false;
                    }
                }
                return true;
            });

            $possibleCount = count($possibleMoves);

            if ($possibleCount === 4 || $possibleCount === 0) {
                continue;
            }

            $move = reset($possibleMoves)['go'];
            $suggestions[($x + $move[0]) . '.' . ($y + $move[1])][] = $xy;
        }

        // apply suggestions that are not contending for the same spot
        $moves = array_filter($suggestions, fn($s) => count($s) === 1);

        foreach ($moves as $location => [$oldXy]) {
            unset($this->elves[$oldXy]);
            [$x, $y] = explode('.', $location);
            $this->elves[$location] = [(int)$x, (int)$y];
        }

        $this->rotateDirections();

        return count($moves);
    }
    protected function getSquareDimensions(): array
    {
        $xs = array_column($this->elves, 0);
        $ys = array_column($this->elves, 1);

        return [[min($xs), min($ys)], [max($xs), max($ys)]];
    }

    protected function rotateDirections(): void
    {
        $this->directions = [
            $this->directions[1],
            $this->directions[2],
            $this->directions[3],
            $this->directions[0],
        ];
    }
}
