<?php

namespace App;

use App\Models\Day11\Monkey;
use Illuminate\Support\Collection;

class Day11 extends Day
{
    public static string $title = 'Monkey in the Middle';

    private Collection $monkeys;

    public function __construct()
    {
        set_time_limit(0);

        parent::__construct();

        $this->monkeys = str($this->input)
            ->explode("\n\n")
            ->map(fn ($definition) => new Monkey($definition))
            ->mapWithKeys(fn ($monkey) => [$monkey->id => $monkey]);

        $modulo = 1;
        $this->monkeys->each(function ($monkey) use (&$modulo) {
            $modulo *= $monkey->divisibleTest;
        });

        $this->monkeys->each(function ($monkey) use ($modulo) {
            $monkey->modulo = $modulo;
            $monkey->monkeyIfTrue = $this->monkeys[$monkey->monkeyIfTrue];
            $monkey->monkeyIfFalse = $this->monkeys[$monkey->monkeyIfFalse];
        });

        dump($this->monkeys);
    }

    public function result1(): int
    {
        return $this->go(20, true, true, true);
    }

    public function result2(): int
    {
        return $this->go(10000, false);
    }

    public function go(int $rounds, bool $reduceWorry, bool $dump = false, bool $dumpAll = false): int
    {
        echo "<pre>";

        for ($round = 1; $round <= $rounds; $round++) {
            if ($dump) {
                echo("\n### Round $round\n");
            }
            $this->monkeys->each(fn ($monkey) => $monkey->go($reduceWorry, $dumpAll));

            if ($dump) {
                $this->monkeys->each(function ($monkey) {
                    echo("Monkey $monkey->id : " . $monkey->getItems()->join(', ') . "\n");
                });
            }
        }

        foreach ($this->monkeys as $monkey) {
            echo("Monkey $monkey->id inspected $monkey->inspections items\n");
        }

        $inspections = $this->monkeys->map(fn ($monkey) => $monkey->inspections)->sort()->reverse()->values();

        echo "</pre>";

        return $inspections[0] * $inspections[1];
    }
}
