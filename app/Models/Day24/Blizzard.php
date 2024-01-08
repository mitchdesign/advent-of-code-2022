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
        if ($this->deltaX !== 0) {
            $x = $this->coordinate->x + ($timePassed * $this->deltaX);

            while ($x < Day24::$minX) {
                $x += Day24::$deltaX;
            }

            while ($x > Day24::$maxX) {
                $x -= Day24::$deltaX;
            }
        } else {
            $x = $this->coordinate->x;
        }

        if ($this->deltaY !== 0) {
            $y = $this->coordinate->y + ($timePassed * $this->deltaY);

            while ($y < Day24::$minY) {
                $y += Day24::$deltaY;
            }

            while ($y > Day24::$maxY) {
                $y -= Day24::$deltaY;
            }
        } else {
            $y = $this->coordinate->y;
        }

        return new Coordinate($x, $y);
    }
}
