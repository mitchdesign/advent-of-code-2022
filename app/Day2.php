<?php

namespace App;

class Day2 extends Day
{
    private array $scores = [
        'AX' => 3 + 1,
        'AY' => 6 + 2,
        'AZ' => 0 + 3,

        'BX' => 0 + 1,
        'BY' => 3 + 2,
        'BZ' => 6 + 3,

        'CX' => 6 + 1,
        'CY' => 0 + 2,
        'CZ' => 3 + 3,
    ];

    private array $outcomes = [
        'AX' => 0 + 3,
        'AY' => 3 + 1,
        'AZ' => 6 + 2,

        'BX' => 0 + 1,
        'BY' => 3 + 2,
        'BZ' => 6 + 3,

        'CX' => 0 + 2,
        'CY' => 3 + 3,
        'CZ' => 6 + 1,
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
