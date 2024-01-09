<?php

namespace App;

use App\Models\Day24\Blizzard;
use App\Models\Day24\Coordinate;
use App\Models\Day24\State;

class Day24 extends Day
{
    public static string $title = 'Blizzard Basin';

    public static Coordinate $start;
    public static Coordinate $goal;

    public static int $minX;
    public static int $maxX;
    public static int $minY;
    public static int $maxY;
    public static int $deltaX;
    public static int $deltaY;

    public static int $blizzardOptionSize;

    public static array $blizzards;
    public static array $blizzardStates = [];

    public function __construct()
    {
        parent::__construct();

//        $this->input = '#.######
//#>>.<^<#
//#.<..<<#
//#>v.><>#
//#<^v^^>#
//######.#';

        $lines = str(trim($this->input))
            ->explode("\n");

        self::$minX = 1;
        self::$maxX = strlen($lines->first()) - 2;
        self::$minY = 1;
        self::$maxY = $lines->count() - 2;

        self::$deltaX = self::$maxX - self::$minX + 1;
        self::$deltaY = self::$maxY - self::$minY + 1;

        $smallest = min(self::$deltaX, self::$deltaY);
        $biggest = max(self::$deltaX, self::$deltaY);

        $i = 1;
        while (($smallest * $i) % $biggest !== 0) {
            $i++;
        }
        self::$blizzardOptionSize = $smallest * $i;

        self::$blizzards = $lines->map(fn ($line, $lineIndex) => str($line)->split(1)->map(fn  ($char, $charIndex) =>
            match ($char) {
                '<' => new Blizzard(new Coordinate($charIndex, $lineIndex), self::$deltaX - 1, 0),
                '>' => new Blizzard(new Coordinate($charIndex, $lineIndex), 1, 0),
                '^' => new Blizzard(new Coordinate($charIndex, $lineIndex), 0, self::$deltaY - 1),
                'v' => new Blizzard(new Coordinate($charIndex, $lineIndex), 0, 1),
                default => null,
            }
        ))->flatten()->filter()->toArray();

        self::$start = new Coordinate(
            str($lines->first())->split(1)->takeUntil(fn ($c) => $c === '.')->count(),
            0
        );
        self::$goal = new Coordinate(
            str($lines->last())->split(1)->takeUntil(fn ($c) => $c === '.')->count(),
            $lines->count() - 1
        );
    }

    public function result1(): int
    {
        return $this->findShortestRoute(self::$start, self::$goal, 0);
    }

    public function result2(): int
    {
        $run1 = $this->findShortestRoute(self::$start, self::$goal, 0);
        $run2 = $this->findShortestRoute(self::$goal, self::$start, $run1);
        $run3 = $this->findShortestRoute(self::$start, self::$goal, $run2);

        return $run3;
    }

    protected function findShortestRoute(Coordinate $from, Coordinate $to, int $startTime): int
    {
        $visitedStates = [];

        $state = new State($from, $startTime);

        $queue = new \SplPriorityQueue();
        $queue->insert($state, $state->getQueueHeuristic($to));

        /** @var State $state */
        while ($state = $queue->extract()) {
            if ($state->coordinate->equals($to)) {
                return $state->timePassed;
            }

            /** @var State $nextState */
            foreach ($state->getPossibleNextStates($visitedStates) as $nextState) {
                $visitedStates[$nextState->getStateId()] = true;
                $queue->insert($nextState, $nextState->getQueueHeuristic($to));
            }
        }

        throw new \Exception('Found no solution');
    }

    public static function isCoordinateOccupiedAtTime(Coordinate $coordinate, int $timePassed): bool
    {
        return array_key_exists($coordinate->toString(), self::getBlizzardStateAtTime($timePassed));
    }

    private static function getBlizzardStateAtTime(int $timePassed): array
    {
        $timePassed = $timePassed % self::$blizzardOptionSize;

        if (array_key_exists($timePassed, self::$blizzardStates)) {
            return self::$blizzardStates[$timePassed];
        }

        $blizzards = self::$blizzardStates[$timePassed] = array_flip(array_values(array_map(
            fn ($blizzard) => $blizzard->getCoordinateAtTime($timePassed)->toString(), self::$blizzards
        )));

        return $blizzards;
    }
}
