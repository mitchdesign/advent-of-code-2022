<?php

namespace App;

use Illuminate\Support\Collection;

class Day4 extends Day
{
    public static string $title = 'Camp Cleanup';

    public function result1(): int
    {
        return collect(explode("\n", $this->input))
            ->filter(function ($combo) {
                $combo = str($combo)
                    ->trim()
                    ->explode(',')
                    ->map(fn ($section) => str($section)->explode('-'));
                return $this->sectionsOverlapCompletely(...$combo);
            })->count();
    }

    public function result2(): int
    {
        return collect(explode("\n", $this->input))
            ->filter(function ($combo) {
                $combo = str($combo)
                    ->trim()
                    ->explode(',')
                    ->map(fn ($section) => str($section)->explode('-'));
                return $this->sectionsOverlapAtAll(...$combo);
            })->count();
    }

    private function sectionsOverlapAtAll(Collection $section1, Collection $section2): bool
    {
        // if one section is completely before or after the other, then they do not overlap
        return ! ($section1->last() < $section2->first() || $section1->first() > $section2->last());
    }

    private function sectionsOverlapCompletely(Collection $section1, Collection $section2): bool
    {
        // one section must contain the other
        return $this->sectionContains($section1, $section2)
            || $this->sectionContains($section2, $section1);
    }

    private function sectionContains(Collection $section1, Collection $section2): bool
    {
        return $section1->first() <= $section2->first() && $section1->last() >= $section2->last();
    }
}
