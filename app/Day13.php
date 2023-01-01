<?php

namespace App;

use Illuminate\Support\Collection;

class Day13 extends Day
{
    public static string $title = 'Distress Signal';

    public Collection $inputs;

    public function __construct()
    {
        parent::__construct();

        // get the inputs as php arrays in arrays of [input1, input2], with 1-based index
        $this->inputs = str($this->input)
            ->explode("\n\n")
            ->mapWithKeys(fn ($group, $index) => [$index + 1 => str($group)
                ->explode("\n")
                ->map(fn ($line) => json_decode($line))]
            );
    }

    public function result1(): int
    {
        // filter the inputs using a sort function. it outputs -1 if input 1 < input 2. 0 if equal.
        // in both cases the input is in the right order, and so we keep it, to sum the indexes to get the answer.
        return $this->inputs->filter(fn($input) => in_array($this->sort(...$input), [-1, 0]))
            ->keys()
            ->sum();
    }

    public function result2(): int
    {
        return 1;
    }

    public function sort(array $input1, array $input2): int
    {
        return 0;
    }
}
