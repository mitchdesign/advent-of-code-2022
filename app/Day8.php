<?php

namespace App;

use Illuminate\Support\Collection;

class Day8 extends Day
{
    public static string $title = 'Treetop Tree House';

    protected Collection $forest;
    protected Collection $rotatedForest;

    protected array $forestArray;
    protected array $rotatedForestArray;

    protected Collection $visibleTrees;

    public function __construct()
    {
        parent::__construct();

        $this->forest = str($this->input)
            ->trim()
            ->explode("\n")
            ->map(fn ($row) => str($row)->trim()->split(1)->map(fn ($x) => (int) $x));

        $width = $this->forest->first()->count();

        $this->rotatedForest = Collection::range(0, $width - 1)
            ->map(fn ($i) => $this->forest->pluck($i));

        $this->forestArray = $this->forest->toArray();
        $this->rotatedForestArray = $this->rotatedForest->toArray();

        $this->visibleTrees = collect();
    }

    public function result1(): int
    {
        $this->forest->each(fn ($row, $y) => $this->findVisibleFromOutside($row, '', ".{$y}"));
        $this->rotatedForest->each(fn ($col, $x) => $this->findVisibleFromOutside($col, "{$x}.", ''));

        return $this->visibleTrees->unique()->count();
    }

    public function result2(): int
    {
        $maxScenicScore = 0;

        foreach (array_keys($this->forestArray) as $row) {
            foreach (array_keys($this->forestArray[0]) as $col) {
                $maxScenicScore = max($maxScenicScore, $this->getScenicScore($row, $col));
            }
        }

        return $maxScenicScore;
    }

    private function findVisibleFromOutside(Collection $row, string $prefix, string $postfix): void
    {
        $max = -1;
        $row->each(function ($tree, $i) use (&$max, $prefix, $postfix) {
            if ($tree > $max) {
                $this->visibleTrees->add("{$prefix}{$i}{$postfix}");
                $max = $tree;
            }
        });

        $max = -1;
        $row->reverse()->each(function ($tree, $i) use (&$max, $prefix, $postfix) {
            if ($tree > $max) {
                $this->visibleTrees->add("{$prefix}{$i}{$postfix}");
                $max = $tree;
            }
        });
    }

    private function getScenicScore(int $row, int $col): int
    {
        $tree = $this->forestArray[$row][$col];

        $left = array_reverse(array_slice($this->forestArray[$row], 0, $col));
        $right = array_slice($this->forestArray[$row], $col + 1);
        $top = array_reverse(array_slice($this->rotatedForestArray[$col], 0, $row));
        $bottom = array_slice($this->rotatedForestArray[$col], $row + 1);

        $scoreLeft = $this->getScenicCount($tree, $left);
        $scoreRight = $this->getScenicCount($tree, $right);
        $scoreTop = $this->getScenicCount($tree, $top);
        $scoreBottom = $this->getScenicCount($tree, $bottom);

        return $scoreLeft * $scoreRight * $scoreTop * $scoreBottom;
    }

    private function getScenicCount(int $tree, array $view): int
    {
        $horizonFound = false;

        return collect($view)->takeWhile(function ($treeInView) use ($tree, &$horizonFound): bool {
            if ($horizonFound) {
                return false;
            }

            if ($treeInView >= $tree) {
                $horizonFound = true;
            }

            return true;
        })->count();
    }
}
