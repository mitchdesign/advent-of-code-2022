<?php

namespace App\Models\Day19;

class Miner
{
    private int $maxGeodes = 0;
    private array $toVisit = [];
    private array $consideredStates = [];

    private int $maxOreCost = 0;
    private int $maxClayCost = 0;
    private int $maxObsidianCost = 0;

    public function __construct(
        private int $time,
        private array $blueprint,
    )
    {
        $this->maxOreCost = max($this->blueprint['ore.ore'], $this->blueprint['clay.ore'], $this->blueprint['obsidian.ore'], $this->blueprint['geode.ore']);
        $this->maxClayCost = $this->blueprint['obsidian.clay'];
        $this->maxObsidianCost = $this->blueprint['geode.obsidian'];
    }

    public function mine(): int
    {
        dump('Blueprint', $this->blueprint);

        $this->addToVisit([
            'time' => $this->time,
            'ore' => 0,
            'oreR' => 1,
            'clay' => 0,
            'clayR' => 0,
            'obsidian' => 0,
            'obsidianR' => 0,
            'geode' => 0,
            'geodeR' => 0,
        ]);

        while ($next = array_pop($this->toVisit)) {
            $this->visit($next);
        }

        dump('Blueprint ' . $this->blueprint['id'] . ' : ' . $this->maxGeodes);

        return $this->maxGeodes;
    }

    private function addToVisit(array $state): void
    {
        // check if we already visited this state. if that one was equal or better on time and/or geodes, we cannot improve on it so we skip.

        // This probably helps but for the second puzzle the state space becomes too large

//        $stateSummary = "{$state['ore']}.{$state['oreR']}.{$state['clay']}.{$state['clayR']}.{$state['obsidian']}.{$state['obsidianR']}.{$state['geodeR']}";
//        $consideredState = $this->consideredStates[$stateSummary] ?? null;
//
//        if ($consideredState) {
//            if ($consideredState['time'] >= $state['time'] && $consideredState['geode'] >= $state['geode']) {
//                return;
//            }
//        }
//
//        // there was no better record for this state, so lets record this one, we don't need to try it again.
//
//        $this->consideredStates[$stateSummary] = ['time' => $state['time'], 'geode' => $state['geode']];

        // check if we can even make the current maxGeodes from this position. if we cannot, we skip.

        if ($state['clayR'] == 0) {
            $delay = 2;
        } else if ($state['obsidianR'] == 0) {
            $delay = 1;
        } else {
            $delay = 0;
        }

        $geodesThatCanBeAdded = $state['time'] * $state['geodeR'];
        $geodesThatCanBeAdded += ($state['time'] - $delay) * ($state['time'] - $delay - 1) / 2;
        if ($geodesThatCanBeAdded + $state['geode'] <= $this->maxGeodes) {
            return;
        }

        $this->toVisit[] = $state;
    }

    private function visit(array $state)
    {
        $buildOptions = [];

        if ($state['time'] == 1) {
            // this was the last minute. nothing we build will have an effect, so do not build. we are done.
            // just count the geodes we are adding in this last minute

            $state['geode'] += $state['geodeR'];

            if ($state['geode'] > $this->maxGeodes) {
                $this->maxGeodes = $state['geode'];
            }

            return;
        }

        // decide which robot to build next. anything that makes sense we will add as a visiting option

        // ORE robot
        if ($state['oreR'] < $this->maxOreCost) {
            // skip if we got enough ore to build a robot each time
            // also, building ore robots in the second to last step does not help because any robot built in the last step will not add anything
            $stepsNeeded = max(1,
                1 + ceil(($this->blueprint['ore.ore'] - $state['ore']) / $state['oreR'])
            );

            if ($state['time'] > 1 + $stepsNeeded) {
                $buildOptions[] = [
                    'time' => $state['time'] - $stepsNeeded,
                    'ore' => $state['ore'] + $state['oreR'] * $stepsNeeded - $this->blueprint['ore.ore'],
                    'oreR' => $state['oreR'] + 1,
                    'clay' => $state['clay'] + $state['clayR'] * $stepsNeeded,
                    'clayR' => $state['clayR'],
                    'obsidian' => $state['obsidian'] + $state['obsidianR'] * $stepsNeeded,
                    'obsidianR' => $state['obsidianR'],
                    'geode' => $state['geode'] + $state['geodeR'] * $stepsNeeded,
                    'geodeR' => $state['geodeR'],
                ];
            }
        }

        // CLAY robot
        if ($state['clayR'] < $this->maxClayCost) {
            // skip if we got enough clay to build a robot each time
            // also, building clay robots in the second or third to last step does not help because clay is needed for obsidian, obsidian for geode and only after that can we mine it
            $stepsNeeded = max(
                1,
                1 + ceil(($this->blueprint['clay.ore'] - $state['ore']) / $state['oreR'])
            );

            if ($state['time'] > 2 + $stepsNeeded) {
                $buildOptions[] = [
                    'time' => $state['time'] - $stepsNeeded,
                    'ore' => $state['ore'] + $state['oreR'] * $stepsNeeded - $this->blueprint['clay.ore'],
                    'oreR' => $state['oreR'],
                    'clay' => $state['clay'] + $state['clayR'] * $stepsNeeded,
                    'clayR' => $state['clayR'] + 1,
                    'obsidian' => $state['obsidian'] + $state['obsidianR'] * $stepsNeeded,
                    'obsidianR' => $state['obsidianR'],
                    'geode' => $state['geode'] + $state['geodeR'] * $stepsNeeded,
                    'geodeR' => $state['geodeR'],
                ];
            }
        }

        // OBSIDIAN robot
        if ($state['clayR'] && $state['obsidianR'] < $this->maxObsidianCost) {
            // skip if we got enough clay to build a robot each time
            // also, building obsidian robots in the second to last step does not help because the geode robot built in the last step will not add anything
            $stepsNeeded = max(
                1,
                1 + ceil(($this->blueprint['obsidian.ore'] - $state['ore']) / $state['oreR']),
                1 + ceil(($this->blueprint['obsidian.clay'] - $state['clay']) / $state['clayR'])
            );

            if ($state['time'] > 1 + $stepsNeeded) {
                $buildOptions[] = [
                    'time' => $state['time'] - $stepsNeeded,
                    'ore' => $state['ore'] + $state['oreR'] * $stepsNeeded - $this->blueprint['obsidian.ore'],
                    'oreR' => $state['oreR'],
                    'clay' => $state['clay'] + $state['clayR'] * $stepsNeeded - $this->blueprint['obsidian.clay'],
                    'clayR' => $state['clayR'],
                    'obsidian' => $state['obsidian'] + $state['obsidianR'] * $stepsNeeded,
                    'obsidianR' => $state['obsidianR'] + 1,
                    'geode' => $state['geode'] + $state['geodeR'] * $stepsNeeded,
                    'geodeR' => $state['geodeR'],
                ];
            }
        }

        // GEODE robot
        if ($state['obsidianR']) {
            $stepsNeeded = max(
                1,
                    1 + ceil(($this->blueprint['geode.ore'] - $state['ore']) / $state['oreR']),
                    1 + ceil(($this->blueprint['geode.obsidian'] - $state['obsidian']) / $state['obsidianR'])
                );

            if ($state['time'] > $stepsNeeded) {
                $buildOptions[] = [
                    'time' => $state['time'] - $stepsNeeded,
                    'ore' => $state['ore'] + $state['oreR'] * $stepsNeeded - $this->blueprint['geode.ore'],
                    'oreR' => $state['oreR'],
                    'clay' => $state['clay'] + $state['clayR'] * $stepsNeeded,
                    'clayR' => $state['clayR'],
                    'obsidian' => $state['obsidian'] + $state['obsidianR'] * $stepsNeeded - $this->blueprint['geode.obsidian'],
                    'obsidianR' => $state['obsidianR'],
                    'geode' => $state['geode'] + $state['geodeR'] * $stepsNeeded,
                    'geodeR' => $state['geodeR'] + 1,
                ];
            }
        }

        // do nothing. just fast forward to the last minute
        if ($buildOptions) {
            foreach ($buildOptions as $buildOption) {
                $this->addToVisit($buildOption);
            }
        } else {
            $stepsTo1 = $state['time'] - 1;
            $this->addToVisit([
                'time' => 1,
                'ore' => $state['ore'] + $state['oreR'] * $stepsTo1,
                'oreR' => $state['oreR'],
                'clay' => $state['clay'] + $state['clayR'] * $stepsTo1,
                'clayR' => $state['clayR'],
                'obsidian' => $state['obsidian'] + $state['obsidianR'] * $stepsTo1,
                'obsidianR' => $state['obsidianR'],
                'geode' => $state['geode'] + $state['geodeR'] * $stepsTo1,
                'geodeR' => $state['geodeR'],
            ]);
        }
    }
}
