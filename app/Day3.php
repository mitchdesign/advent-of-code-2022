<?php

namespace App;

use Illuminate\Support\Str;

class Day3 extends Day
{
    public function result1()
    {
        return collect(explode("\n", $this->input))
            ->filter()
            ->map(function ($rucksack) {
                $itemSets = collect(str_split(trim($rucksack))) // I don't know of a Laravel way to split a string into its characters
                    ->splitIn(2)
                    ->map(fn ($set) => $set->unique()); // tricky one: the same item can be in a compartment multiple times

                $doubleItem = $itemSets->first()
                    ->intersect($itemSets->last())
                    ->sole();

                $asciiValue = ord($doubleItem);

                $value = $asciiValue < ord('a')
                    ? $asciiValue - ord('A') + 27
                    : $asciiValue - ord('a') + 1;

                return $value;
            })->sum();
    }

    public function result2()
    {
        return collect(explode("\n", $this->input))
            ->filter()
            ->chunk(3)
            ->map(function ($rucksacks) {
                $rucksacks = $rucksacks->map(fn ($rucksack) => array_unique(str_split($rucksack)))
                    ->values();

                $uniqueItem = collect(array_intersect(...$rucksacks))->sole();  // using collect()->sole() as a check that there is exactly one

                $asciiValue = ord($uniqueItem);

                $value = $asciiValue < ord('a')
                    ? $asciiValue - ord('A') + 27
                    : $asciiValue - ord('a') + 1;

                return $value;
            })->sum();
    }
}
