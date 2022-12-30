<?php

namespace App;

class Day12 extends Day
{
    public static string $title = 'Hill Climbing Algorithm';

    // keep the height map in a 2 dimensional array
    public array $map;

    // the queue which we keep organised so the last item is the next one to try
    public array $queue;

    // for each node we keep track of how we got to it (updated for the current path)
    public array $cameFrom;

    // for each node we keep track of how many steps it took to get here via the current path
    public array $stepsTo;

    // for each node we estimate the shortest possible route to the goal
    public array $estimationToGoal;

    // starting point to go from
    public string $start;

    // end point- when we get here we are done (in puzzle 1)
    public string $goal;

    // function to estimate the shortest distance from a node to a goal
    public \Closure $estimateGoal;

    // function to check if we reached a goal
    public \Closure $goalReached;

    // function to check if a found neighbor (next on path) is valid to select
    public \Closure $neighborValid;


    public function __construct()
    {
        parent::__construct();

//        $this->input = 'Sabqponm
//abcryxxl
//accszExk
//acctuvwj
//abdefghi';

        $this->map = str(trim($this->input))
            ->explode("\n")
            ->map(fn($line, $lineIndex) => str($line)->split(1)->map(function ($char, $charIndex) use ($lineIndex) {
                if ($char == 'S') {
                    $this->start = "{$lineIndex}|{$charIndex}";
                    $char = 'a';
                }
                if ($char == 'E') {
                    $this->goal = "{$lineIndex}|{$charIndex}";
                    $char = 'z';
                }
                return ord($char) - 97;
            }))->toArray();

    }

    public function result1(): int
    {
        $this->estimateGoal = fn (string $node) => $this->estimateDistanceBetween($node, $this->goal);
        $this->goalReached = fn (string $node) => $node === $this->goal;
        $this->neighborValid = fn (int $currentHeight, int $neighborHeight) => $neighborHeight <= ($currentHeight + 1);

        $this->cameFrom = [$this->start => null];
        $this->stepsTo = [$this->start => 0];
        $this->estimationToGoal = [$this->start => ($this->estimateGoal)($this->start)];
        $this->queue = $this->estimationToGoal;

        return $this->findRoute();
    }

    public function result2(): int
    {
        $this->start = $this->goal; // start from the end for this one
        $this->goal = '';

        // the estimated goal is the minimum distance to an 'a'
        $this->estimateGoal = fn (string $node) => $this->getDistanceToClosestA($node);

        // the end is reached as soon as we get to a 'a' position (height 0)
        $this->goalReached = function (string $node) {
            [$line, $char] = explode('|', $node);
            return $this->map[$line][$char] === 0;
        };

        // valid neighbors are the other way around: we cannot go down by more than 1
        $this->neighborValid = fn (int $currentHeight, int $neighborHeight) => $currentHeight <= ($neighborHeight + 1);

        $this->cameFrom = [$this->start => null];
        $this->stepsTo = [$this->start => 0];
        $this->estimationToGoal = [$this->start => ($this->estimateGoal)($this->start)];
        $this->queue = $this->estimationToGoal;

        return $this->findRoute();
    }

    public function findRoute()
    {
        while ($this->queue) {
            $current = $this->getNextFromQueue();
            $stepsToCurrent = $this->stepsTo[$current];

            if (($this->goalReached)($current)) {
                return $this->stepsTo[$current];
            }

            foreach ($this->getNeighborsWithStepsFor($current) as $neighbor => $deltaToNeighbor) {
                $previousStepsToNeighbor = $this->stepsTo[$neighbor] ?? null;
                $newStepsToNeighbor = $stepsToCurrent + $deltaToNeighbor;

                if ($previousStepsToNeighbor === null || $previousStepsToNeighbor > $newStepsToNeighbor) {
                    $this->cameFrom[$neighbor] = $current;
                    $this->estimationToGoal[$neighbor] = $est = ($this->estimateGoal)($neighbor);
                    $this->stepsTo[$neighbor] = $newStepsToNeighbor;
                    $this->addToQueue($neighbor, $newStepsToNeighbor + $est);
                }
            }
        }

        throw new \Exception('Route failed');
    }

    public function estimateDistanceBetween(string $node1, string $node2): int
    {
        [$line1, $char1] = explode('|', $node1);
        [$line2, $char2] = explode('|', $node2);

        return abs($line1 - $line2) + abs($char1 - $char2);
    }

    public function getNextFromQueue(): string
    {
        $next = array_key_last($this->queue);
        $this->queue = array_slice($this->queue, 0, -1);

        return $next;
    }

    public function getHeightAt(int $line, int $char): ?int
    {
        return $this->map[$line][$char] ?? null;
    }

    public function getNeighborsWithStepsFor(string $node): array
    {
        [$line, $char] = explode('|', $node);
        $height = $this->getHeightAt($line, $char);

        $neighbors = [];

        foreach ([
            [$line - 1, $char],
            [$line + 1, $char],
            [$line, $char - 1],
            [$line, $char + 1],
        ] as $nc) {
            $neighborHeight = $this->getHeightAt(...$nc);

            if ($neighborHeight !== null && ($this->neighborValid)($height, $neighborHeight)) {
                $neighbors[$n = join('|', $nc)] = 1;
            }
        }

        return $neighbors;
    }

    public function addToQueue(string $node, int $est): void
    {
        // add node to the queue after the last node having same or higher value
        $count = 0;

        foreach ($this->queue as $value) {
            if ($value < $est) {
                break;
            }
            $count++;
        }

        $this->queue = array_merge(
            array_slice($this->queue, 0, $count),
            [$node => $est],
            array_slice($this->queue, $count)
        );
    }

    public function getDistanceToClosestA(string $node)
    {
        [$line, $char] = explode('|', $node);

        $currentDistance = 1;

        while (true) {
            for ($l = $line - $currentDistance; $l <= $line + $currentDistance; $l++) {
                foreach ([$char - ($currentDistance - $l), $char + ($currentDistance - $l)] as $c) {
                    if ($this->map[$l][$c] ?? null === 0) {
                        return $currentDistance;
                    }
                }
            }
            $currentDistance++;
        }
    }
}
