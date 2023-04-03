<?php

namespace App\Models\Day16;

class Optimizer
{
    public int $bestScore = 0;
    public bool $echo = false;

    public function __construct(
        public int $timeLeft,
        public array $distanceMap,
    ) {}

    public function optimize(array $toVisit): int
    {
        $this->bestScore = 0;

        $this->visit(0, $this->timeLeft, 0, 'AA', $toVisit);

        return $this->bestScore;
    }

    protected function visit(int $score, int $timeLeft, int $depth, string $current, array $toVisit): void
    {
        if ($this->echo) echo "{$timeLeft} left. ";

        if (count($toVisit) == 0) {
            if ($this->echo) echo "No more nodes to visit.\n";
            return;
        }

        foreach ($toVisit as $valve => $rate) {
            if ($this->echo) echo "\n" . str_repeat(' ', $depth);
            if ($this->echo) echo "{$valve} ({$rate}): ";

            $distance = $this->distanceMap[$current][$valve];
            if ($distance + 1 >= $timeLeft) {
                if ($this->echo) echo "Not enough time.\n";
                continue;
            }

            $newScore = $score + ($timeLeft - ($distance + 1)) * $rate;
            if ($this->echo) echo "New score {$newScore}. ";

            if ($newScore > $this->bestScore) {
                $this->bestScore = $newScore;
                if ($this->echo) echo "BEST! ";
            }

            $this->visit($newScore, $timeLeft - ($distance + 1), $depth + 1, $valve, array_diff_key($toVisit, [$valve => 0]));
        }
    }
}
