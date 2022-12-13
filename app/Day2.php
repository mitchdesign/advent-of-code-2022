<?php

namespace App;

class Day2 extends Day
{
    private array $scores = [
        'AX' => 3 + 1,  // rock-rock = draw
        'AY' => 6 + 2,  // rock-paper = win
        'AZ' => 0 + 3,  // rock-scissors = loose

        'BX' => 0 + 1,  // paper-rock = loose
        'BY' => 3 + 2,  // paper-paper = draw
        'BZ' => 6 + 3,  // paper-scissors = win

        'CX' => 6 + 1,  // scissors-rock = win
        'CY' => 0 + 2,  // scissors-paper = loose
        'CZ' => 3 + 3,  // scissors-scissors = draw
    ];

    private array $outcomes = [
        'AX' => 0 + 3,  // rock, to loose: scissors
        'AY' => 3 + 1,  // rock, to draw: rock
        'AZ' => 6 + 2,  // rock, to win: paper

        'BX' => 0 + 1,  // paper, to loose: rock
        'BY' => 3 + 2,  // paper, to draw: paper
        'BZ' => 6 + 3,  // paper, to win: scissors

        'CX' => 0 + 2,  // scissors, to loose: paper
        'CY' => 3 + 3,  // scissors, to draw: scissors
        'CZ' => 6 + 1,  // scissors, to win: rock
    ];

    public function result1()
    {
        return collect(explode("\n", $this->input))
            ->filter()
            ->map(function ($val) {
                $val = preg_replace('/[^ABCXYZ]/', '', $val);
                return $this->scores[$val];
            })
            ->sum();
    }

    public function result2()
    {
        return collect(explode("\n", $this->input))
            ->filter()
            ->map(function ($val) {
                $val = preg_replace('/[^ABCXYZ]/', '', $val);
                return $this->outcomes[$val];
            })
            ->sum();
    }
}
