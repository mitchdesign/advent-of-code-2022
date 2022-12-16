<?php

namespace App;

use Illuminate\Support\Collection;

class Day5 extends Day
{
    public static string $title = 'Supply Stacks';

    private Collection $stacks;
    private Collection $moves;

    public function __construct()
    {
        parent::__construct();

        [$stacksInput, $movesInput] = explode("\n\n", $this->input);

        // prepare stacks into collections keyed by 1..9
        // example input:
        // [B] [Q] [V]     [S]
        // [S] [P] [T] [R] [M]     [D]
        // [D] [S] [L] [J] [L] [G] [G] [F] [R]
        // [G] [Z] [C] [H] [C] [R] [H] [P] [D]
        //  1   2   3   4   5   6   7   8   9

        $stacks = Collection::range(1, 9)
            ->mapWithKeys(fn ($i) => [$i => collect()]);

        collect(str($stacksInput)->explode("\n"))
            ->slice(0, -1) // drop the line of keys that is printed below the stacks
            ->each(function ($line) use ($stacks) {
                $line = str($line)
                    ->split(4)
                    ->each(fn ($val, $key) => trim($val) === '' || $stacks->get($key + 1)->prepend(trim($val)));
            });

        $this->stacks = $stacks;

        // prepare moves

        $moves = collect();

        collect(str($movesInput)->explode("\n"))
            ->each(function ($line) use ($moves) {
                $move = [];
                if (preg_match('/move (?<num>\d+) from (?<from>\d) to (?<to>\d)/', $line, $move)) {
                    $moves->add($move);
                }
            });

        $this->moves = $moves;
    }

    public function result1(): string
    {
        $this->moves->each(function ($move) {
            foreach (range(1, $move['num']) as $count) {
                $box = $this->stacks->get($move['from'])->pop();
                $this->stacks->get($move['to'])->add($box);
            }
        });

        return $this->stacks->map(fn ($stack) => $stack->last())->join('');
    }

    public function result2(): string
    {
        $this->moves->each(function ($move) {
            $from = $this->stacks->get($move['from']);
            $to = $this->stacks->get($move['to']);

            $boxes = $from->splice(-1 * $move['num']); // splice removes from original
            foreach ($boxes as $box) {
                $to->add($box); // annoying that there does not seem to be an 'append' for multiple
            }
        });

        return $this->stacks->map(fn ($stack) => $stack->last())->join('');
    }
}
