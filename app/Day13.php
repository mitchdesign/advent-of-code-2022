<?php

namespace App;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Day13 extends Day
{
    public static string $title = 'Distress Signal';

    public Collection $inputs;

    public function __construct()
    {
        parent::__construct();

        // get the inputs as php arrays in arrays of [input1, input2], with 1-based index
        $this->inputs = str(trim($this->input))
            ->explode("\n\n")
            ->mapWithKeys(fn ($group, $index) => [$index + 1 => str($group)
                ->explode("\n")
                ->map(fn ($line) => json_decode($line))]
            );
    }

    public function result1(): int
    {
        // filter the inputs using a sort function.
        // if sort function returns <1 or 0 then the inputs are in the right order.
        return $this->inputs->filter(fn($input) => $this->sort(...$input) <= 0)
            ->keys()
            ->sum();
    }

    public function result2(): int
    {
        /* @var \Illuminate\Support\Collection $sorted */
        $sorted = $this->inputs->flatten(1) // put the duos of inputs into 1 collection
            ->add([[2]])
            ->add([[6]])
            ->sort([$this, 'sort'])
            ->prepend('') // the puzzle says items are 1-indexed, add dummy in front
            ->values();

        $index1 = $sorted->search([[2]]);
        $index2 = $sorted->search([[6]]);

        return $index1 * $index2;
    }

    // sorting callback function.
    // it returns <1 if input 1 smaller than input2, >1 if larger, 0 if equal
    public function sort(int|array $input1, int|array $input2): int
    {
        if (is_int($input1) && is_int($input2)) {
            return $input1 - $input2;
        }

        $input1 = Arr::wrap($input1);
        $input2 = Arr::wrap($input2);

        $i = 0;
        while (isset($input1[$i]) && isset($input2[$i])) {
            $sort = $this->sort($input1[$i], $input2[$i]);
            if ($sort !== 0) {
                return $sort;
            }
            $i++;
        }

        // end of array. if length of input1 is longer, then it is left is larger (return 1)
        // so we return the comparison of the 2 lengths.
        return count($input1) - count($input2);
    }
}
