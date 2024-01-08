<?php

namespace App\Models\Day24;

use App\Day;
use App\Day24;

class Coordinate
{
    public function __construct(
        public readonly int $x,
        public readonly int $y)
    {
    }

    public static function make(int $x, int $y): ?self
    {
        if (Day24::$start->is($x, $y) || Day24::$goal->is($x, $y)) {
            return new self($x, $y);
        }

        if ($x < Day24::$minX || $x > Day24::$maxX || $y < Day24::$minY || $y > Day24::$maxY) {
            return null;
        }

        return new self($x, $y);
    }

    public function up(): ?self
    {
        return self::make($this->x, $this->y - 1);
    }

    public function down(): ?self
    {
        return self::make($this->x, $this->y + 1);
    }

    public function left(): ?self
    {
        return self::make($this->x - 1, $this->y);
    }

    public function right(): ?self
    {
        return self::make($this->x + 1, $this->y);
    }

    public function toString(): string
    {
        return "{$this->x}:{$this->y}";
    }

    public function getDistanceToGoal(): int
    {
        return abs(Day24::$goal->x - $this->x) + abs(Day24::$goal->y - $this->y);
    }

    public function isGoal(): bool
    {
        return Day24::$goal->toString() === $this->toString();
    }

    private function is(int $x, int $y)
    {
        return $this->x === $x && $this->y === $y;
    }
}
