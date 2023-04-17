<?php

namespace App;

class Day19 extends Day
{
    public static string $title = 'Not Enough Minerals';

    protected array $blueprints = [];
    protected int $time = 24;

    public function __construct()
    {
        parent::__construct();

        $this->input = 'Blueprint 1: Each ore robot costs 4 ore. Each clay robot costs 2 ore. Each obsidian robot costs 3 ore and 14 clay. Each geode robot costs 2 ore and 7 obsidian.
Blueprint 2: Each ore robot costs 2 ore. Each clay robot costs 3 ore. Each obsidian robot costs 3 ore and 8 clay. Each geode robot costs 3 ore and 12 obsidian.';

        $this->input = trim($this->input);

        $this->blueprints = $this->parseInput();

        dd($this->blueprints);
    }

    public function puzzle1(): int
    {
        return 1;
    }

    public function puzzle2(): int
    {
        return 1;
    }

    protected function parseInput()
    {
        return str($this->input)->split('/\n/')->map(function ($line) {
            preg_match('/ore robot costs (\d+) ore.*clay robot costs (\d+) ore.*obsidian robot costs (\d+) ore and (\d+) clay.*geode robot costs (\d+) ore and (\d+) obsidian/', $line, $matches);
            return [
                'ore.ore' => (int) $matches[1],
                'clay.ore' => (int) $matches[2],
                'obsidian.ore' => (int) $matches[3],
                'obsidian.clay' => (int) $matches[4],
                'geode.ore' => (int) $matches[5],
                'geode.obsidian' => (int) $matches[6],
            ];
        })->toArray();
    }
}
