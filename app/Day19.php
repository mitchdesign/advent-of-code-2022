<?php

namespace App;

use App\Models\Day19\Miner;
use Illuminate\Support\Collection;

class Day19 extends Day
{
    public static string $title = 'Not Enough Minerals';

    protected Collection $blueprints;

    public function __construct()
    {
        parent::__construct();

//        $this->input = 'Blueprint 1: Each ore robot costs 4 ore. Each clay robot costs 2 ore. Each obsidian robot costs 3 ore and 14 clay. Each geode robot costs 2 ore and 7 obsidian.
//Blueprint 2: Each ore robot costs 2 ore. Each clay robot costs 3 ore. Each obsidian robot costs 3 ore and 8 clay. Each geode robot costs 3 ore and 12 obsidian.';

        $this->input = trim($this->input);

        $this->blueprints = $this->parseInput();
    }

    public function puzzle1(): int
    {
        return $this->blueprints->map(fn ($blueprint) => $blueprint['id'] * (new Miner(time: 24, blueprint: $blueprint))->mine())
            ->sum();
    }

    public function puzzle2(): int
    {
        $product = 1;

        $this->blueprints->slice(0,3)
            ->map(fn ($blueprint) => (new Miner(time: 32, blueprint: $blueprint))->mine())
            ->each(function ($result) use (&$product) { $product *= $result; });

        return $product;
    }

    protected function parseInput()
    {
        return str($this->input)->split('/\n/')->map(function ($line) {
            preg_match('/Blueprint (\d+):.*ore robot costs (\d+) ore.*clay robot costs (\d+) ore.*obsidian robot costs (\d+) ore and (\d+) clay.*geode robot costs (\d+) ore and (\d+) obsidian/', $line, $matches);
            return [
                'id' => (int) $matches[1],
                'ore.ore' => (int) $matches[2],
                'clay.ore' => (int) $matches[3],
                'obsidian.ore' => (int) $matches[4],
                'obsidian.clay' => (int) $matches[5],
                'geode.ore' => (int) $matches[6],
                'geode.obsidian' => (int) $matches[7],
            ];
        });
    }
}
