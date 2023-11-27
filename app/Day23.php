<?php

namespace App;

class Day23 extends Day
{
    public static string $title = 'Unstable Diffusion';

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

    }

    public function puzzle1(): int
    {
        return 1;
    }

    public function puzzle2(): int
    {
        return 1;
    }
}
