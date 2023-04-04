<?php

namespace App;

class Day17 extends Day
{
    public static string $title = 'Pyroclastic Flow';

    protected string $wind = '';
    protected int $windCounter = 0;
    protected int $windLength = 0;

    protected array $rocks = [];
    protected int $rockCounter = 0;
    protected int $rocksLength = 0;

    protected array $cave = [];
    protected int $caveWidth = 0;

    protected int $totalRocks = 0;

    public function __construct()
    {
        parent::__construct();

        $this->totalRocks = 2022;

        $this->input = trim($this->input);

        // override for examples
        // $this->input = '>>><<><>><<<>><>>><<<>>><<<><<<>><>><<>>';

        $this->wind = $this->input;
        $this->windLength = strlen($this->wind);

        // shapes of the rocks. uses [h,l]. h is counting up, l is counting right, starting from left bottom [0,0] position.

        $this->rocks = [
            [ 'units' => [ [0,0], [0,1], [0,2], [0,3] ], 'width' => 1 ],
            [ 'units' => [ [0,1], [1,0], [1,1], [1,2], [2,1] ], 'width' => 3 ],
            [ 'units' => [ [0,0], [0,1], [0,2], [1,2], [2,2] ], 'width' => 3 ],
            [ 'units' => [ [0,0], [1,0], [2,0], [3,0] ], 'width' => 1 ],
            [ 'units' => [ [0,0], [0,1], [1,0], [1,1] ], 'width' => 2 ],
        ];
        $this->rocksLength = count($this->rocks);
    }

    public function puzzle1()
    {
        $this->initCave(7);

        $currentRock = 1;

        while ($currentRock <= $this->totalRocks) {
            $rock = $this->getRock();

            $h = $this->caveFloor() + 4;
            $l = 2;

            $this->render($rock, $h, $l);

            $canFall = true;

            do {
                $wind = $this->getWind();

                if ($this->canMove($rock, $h, $l, 0, $wind)) {
                    $l += $wind;
                }

                $this->render($rock, $h, $l);

                if ($this->canMove($rock, $h, $l, -1, 0)) {
                    $h--;
                } else {
                    $canFall = false;
                }

                $this->render($rock, $h, $l);

            } while ($canFall);

            $this->placeRock($rock, $h, $l);

            $currentRock++;

            $this->render();
        }

        return $this->caveFloor();
    }

    public function puzzle2()
    {
        return 1;
    }

    public function canMove($rock, $h, $l, $dh, $dl): bool
    {
        // the rock can fall if all units of the rock, moved down 1, are empty
        foreach ($rock['units'] as $unit) {
            [$uh, $ul] = $unit;

            $newH = $h + $uh + $dh;
            $newL = $l + $ul + $dl;

            if ($newL < 0 || $newL >= $this->caveWidth  || ! empty($this->cave[$newH][$newL])) {
                return false;
            }
        }

        return true;
    }

    public function placeRock($rock, $h, $l): bool
    {
        // the rock can fall if all units of the rock, moved down 1, are empty
        foreach ($rock['units'] as $unit) {
            [$uh, $ul] = $unit;

            $newH = $h + $uh;
            $newL = $l + $ul;

            $this->cave[$newH][$newL] = true;
        }

        return true;
    }

    public function getWind(): int
    {
        $wind = substr($this->wind, $this->windCounter, 1);

        $this->windCounter++;
        if ($this->windCounter == $this->windLength) {
            $this->windCounter = 0;
        }

        return $wind == '<' ? -1 : 1;
    }

    public function getRock(): array
    {
        $rock = $this->rocks[$this->rockCounter];

        $this->rockCounter++;
        if ($this->rockCounter == $this->rocksLength) {
            $this->rockCounter = 0;
        }

        return $rock;
    }

    public function initCave(int $width): void
    {
        $this->caveWidth = $width;

        for ($x = 0; $x < $this->caveWidth; $x++) {
            $this->cave[0][$x] = true;
        }
    }

    public function caveFloor(): int
    {
        return array_key_last($this->cave);
    }

    public function render(array $rock = [], int $h = 0, int $l = 0): void
    {
        return;

        echo "<pre>";

        $renderedRock = [];

        if ($rock) {
            foreach ($rock['units'] as [$uh, $ul]) {
                $renderedRock[$h + $uh][$l + $ul] = '@';
            }
        }

        for ($h = $this->caveFloor() + 8; $h > 0; $h--) {
            echo '|';
            for ($l = 0; $l < $this->caveWidth; $l++) {
                if (! empty($renderedRock[$h][$l])) {
                    echo '@';
                } else {
                    echo empty($this->cave[$h][$l]) ? '.' : '#';
                }
            }
            echo '|' . "\n";
        }
        echo '+' . str_repeat('-', $this->caveWidth) . '+' . "\n";

        echo "</pre>";
    }
}
