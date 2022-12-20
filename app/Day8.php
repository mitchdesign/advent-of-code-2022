<?php

namespace App;

use Illuminate\Support\Collection;

class Day8 extends Day
{
    public static string $title = 'Treetop Tree House';

    protected Collection $forest;
    protected Collection $rotatedForest;

    protected Collection $visibleTrees;

    public function __construct()
    {
        parent::__construct();

        $this->forest = str($this->input)
            ->trim()
            ->explode("\n")
            ->map(fn ($row) => str($row)->trim()->split(1));

        $width = $this->forest->first()->count();

        $this->rotatedForest = Collection::range(0, $width - 1)
            ->map(fn ($i) => $this->forest->pluck($i));

        $this->visibleTrees = collect();
    }

    public function result1(): int
    {
        $this->forest->each(fn ($row, $y) => $this->findVisible($row, '', ".{$y}"));
        $this->forest->each(fn ($row, $y) => $this->findVisible($row->reverse(), '', ".{$y}"));
        $this->rotatedForest->each(fn ($col, $x) => $this->findVisible($col, "{$x}.", ''));
        $this->rotatedForest->each(fn ($col, $x) => $this->findVisible($col->reverse(), "{$x}.", ''));

        return $this->visibleTrees->unique()->count();
    }

    public function result2(): int
    {
        return 1;
    }

    private function findVisible(Collection $row, string $prefix, string $postfix): void
    {
        $max = -1;
        $row->each(function ($tree, $i) use (&$max, $prefix, $postfix) {
            if ($tree > $max) {
                $this->visibleTrees->add("{$prefix}{$i}{$postfix}");
                $max = $tree;
            }
        });
    }
}
