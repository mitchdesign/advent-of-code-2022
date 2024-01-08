<?php

namespace App\Models\Day24;

use App\Day24;

class Blizzard
{
    public function __construct(
        protected readonly Coordinate $coordinate,
        protected readonly int $deltaX,
        protected readonly int $deltaY,
        )
    {
    }

    public function getCoordinateAtTime(int $timePassed): Coordinate
    {
        $x = ($this->coordinate->x - 1 + ($timePassed * $this->deltaX)) % Day24::$deltaX + 1;
        $y = ($this->coordinate->y - 1 + ($timePassed * $this->deltaY)) % Day24::$deltaY + 1;

        return new Coordinate($x, $y);
    }
}
