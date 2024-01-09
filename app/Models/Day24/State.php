<?php

namespace App\Models\Day24;

use App\Day24;

class State
{
    public function __construct(
        public readonly Coordinate $coordinate,
        public readonly int $timePassed
    )
    {
    }

    public function getPossibleNextStates(array $visitedStates): array
    {
        $nextTime = $this->timePassed + 1;

        return array_filter([
            self::make($this->coordinate->up(), $nextTime),
            self::make($this->coordinate->down(), $nextTime),
            self::make($this->coordinate->left(), $nextTime),
            self::make($this->coordinate->right(), $nextTime),
            self::make($this->coordinate, $nextTime),
        ], fn ($state) => $state && ! array_key_exists($state->getStateId(), $visitedStates));
    }

    public static function make(?Coordinate $coordinate, int $timePassed): ?self
    {
        if (! $coordinate) {
            return null;
        }

        if (Day24::isCoordinateOccupiedAtTime($coordinate, $timePassed)) {
            return null;
        }

        return new self($coordinate, $timePassed);
    }

    public function getQueueHeuristic(Coordinate $to): int
    {
        return -1 * ($this->timePassed + $this->coordinate->getDistanceTo($to));
    }

    public function getStateId(): string
    {
        $time = $this->timePassed % Day24::$blizzardOptionSize;
        return "{$time}.{$this->coordinate->x}.{$this->coordinate->y}";
    }
}
