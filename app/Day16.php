<?php

namespace App;

use App\Models\Day16\Optimizer;

class Day16 extends Day
{
    public static string $title = 'Proboscidea Volcanium';

    private array $valves = [];
    private array $distanceMap = [];

    // Note: the logic in this code relies on the fact that the starting point, AA does not have a valve
    // with a rate > 0. so the first step is always to move to another valve. If the first could be opened
    // we would need some extra logic to handle the option of opening or moving to another (and potentially
    // even coming back to open AA later).

    public function __construct()
    {
        parent::__construct();

        set_time_limit(0);

//        $this->input = 'Valve AA has flow rate=0; tunnels lead to valves DD, II, BB
//Valve BB has flow rate=13; tunnels lead to valves CC, AA
//Valve CC has flow rate=2; tunnels lead to valves DD, BB
//Valve DD has flow rate=20; tunnels lead to valves CC, AA, EE
//Valve EE has flow rate=3; tunnels lead to valves FF, DD
//Valve FF has flow rate=0; tunnels lead to valves EE, GG
//Valve GG has flow rate=0; tunnels lead to valves FF, HH
//Valve HH has flow rate=22; tunnel leads to valve GG
//Valve II has flow rate=0; tunnels lead to valves AA, JJ
//Valve JJ has flow rate=21; tunnel leads to valve II';

        $valves = $this->getValves($this->input);
        $this->distanceMap = $this->getDistances($valves);

        // keep only the valves that have rate > 0
        // in an easy format of name => rate
        $this->valves = collect($valves)->filter(fn ($v) => $v['rate'] > 0)->map(fn ($v) => $v['rate'])->toArray();
    }

    public function puzzle1()
    {
        echo "<pre>";
        $optimizer = new Optimizer(30, $this->distanceMap);
        echo "</pre>";

        return $optimizer->optimize($this->valves);
    }

    public function puzzle2()
    {
        // Principle of puzzle 2:
        // We dont care about what happens when. We just need to know which valves person A (protagonist) will try to open,
        // and which valves are for person B (Elephant). We will try to optimize each possible split. And for each split
        // we will optimize both persons to get the maximum result for their assigned valves.
        // This could be optimized by trying to cut out combinations that are clearly inefficient, but that would take more logic.

        echo "<pre>";

        $optimizer1 = new Optimizer(26, $this->distanceMap);
        $optimizer2 = new Optimizer(26, $this->distanceMap);

        $bestTotal = 0;

        // We take the power of count($this->valves - 1) because we can omit 1/2 of cases
        // because they would only mean the same cases but swapping the two sets completely.
        // In other words: by keeping the last item always in set A we do try all permutations
        // but not again the same set only swapping sides.

        $count = pow(2, count($this->valves) - 1) - 1;

        $valveKeys = $this->keyValvesByPowerOf2($this->valves);

        echo "Count: {$count}\n";

        for ($i = 0; $i < $count; $i++) {
            echo "{$i} ";
            [$valves1, $valves2] = $this->splitValves($valveKeys, $this->valves, $i);
            echo "Optimizing " . join(',', array_keys($valves1)) . ' and ' . join(',', array_keys($valves2));
            $total = $optimizer1->optimize($valves1) + $optimizer2->optimize($valves2);

            echo " {$total}";

            if ($total > $bestTotal) {
                $bestTotal = $total;
                echo " NEW BEST";
            }

            echo "\n";
        }

        echo "</pre>";

        return $bestTotal;
    }

    private function keyValvesByPowerOf2(array $valves): array
    {
        // In order to quickly split the valves in 2 sets based on a counter, we create a helper index e.g.
        // 1 => AA
        // 2 => BB
        // 4 => CC
        // ...
        // Then we can put the valves in 2 buckets by checking for each key if it is a binary AND compared to
        // the counter. E.g. counter = 5 (101) then AA and CC are in group 1, BB in group 2.

        $index = 1;

        $valveKeys = [];

        foreach (array_keys($valves) as $v) {
            $valveKeys[$index] = $v;
            $index *= 2;
        }

        return $valveKeys;
    }

    private function splitValves(array $keys, array $valves, int $iteration): array
    {
        // For each iteration split the valves in 2 groups using the binary index
        // we made in keyValvesByPowerOf2()

        $valves1 = [];
        $valves2 = [];

        foreach ($keys as $index => $valveKey) {
           if ($iteration & $index) {
               $valves1[$valveKey] = $valves[$valveKey];
           } else {
               $valves2[$valveKey] = $valves[$valveKey];
           }
        }

        return [$valves1, $valves2];
    }

    private function getValves(string $input): array
    {
        return str($input)->trim()->split("/\n/")->mapWithKeys(function ($line) {
            preg_match('/Valve (?P<valve>\w+) has flow rate=(?P<rate>\d+); tunnels? leads? to valves? (?P<to>.*)$/', $line, $matches);
            return [
                $matches['valve'] => [
                    'rate' => (int) $matches['rate'],
                    'to' => str($matches['to'])->split('/, /')->map(fn ($s) => trim($s))->toArray(),
                ]
        ];
        })->toArray();
    }

    private function getDistances(array $valves): array
    {
        return collect($valves)
            ->map(fn ($data, $key) => $this->getDistancesFromValve($key, $valves))
            ->toArray();
    }

    private function getDistancesFromValve(string $valve, array $valves)
    {
        $toGo = array_keys($valves);
        $toGo = array_filter($toGo, fn ($v) => $v !== $valve);

        $distances = [];

        $current = [$valve];
        $currentDistance = 0;

        while (count($toGo) > 0) {
            $currentDistance++;

            $next = array_map(fn ($c) => $valves[$c]['to'], $current);
            $next = array_merge(...$next);

            $next = array_unique(array_intersect($next, $toGo));

            $toGo = array_diff($toGo, $next);

            foreach ($next as $n) {
                $distances[$n] = $currentDistance;
            }

            $current = $next;
        }

        return $distances;
    }
}
