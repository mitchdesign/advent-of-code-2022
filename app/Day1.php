<?php

namespace App;

class Day1 extends Day
{
    public function result1()
    {
        return collect(explode("\n\n", $this->input))
            ->map(fn ($val) => collect(explode("\n", $val))->sum())
            ->max();
    }

    public function result2()
    {
        return collect(explode("\n\n", $this->input))
            ->map(fn ($val) => collect(explode("\n", $val))->sum())
            ->sort()
            ->slice(-3)
            ->sum();
    }
}
