<?php

namespace App;

use Illuminate\Support\Collection;

class Day15 extends Day
{
    public static string $title = 'Beacon Exclusion Zone';

    public Collection $sensorsBeacons;

    public function __construct()
    {
        parent::__construct();

        $this->sensorsBeacons = str(trim($this->input))
            ->explode("\n")
            ->map(function ($line) {
                if (! preg_match('/Sensor at x=(?P<sx>[-\d]+), y=(?P<sy>[-\d]+): closest beacon is at x=(?P<bx>[-\d]+), y=(?P<by>[-\d]+)/', $line, $matches)) {
                    throw new \RuntimeException('Unmatched definition');
                };
                $matches['r'] = abs($matches['sx'] - $matches['bx']) + abs($matches['sy'] - $matches['by']);
                return $matches;
            });
    }

    public function result1(): int
    {
        $y = 2000000;

        $startX = null;
        $activeAreaCount = 0;
        $fieldCount = 0;

        foreach ($this->getAreaDataForLine($y) as $point) {
            if (is_int($point)) {
                $startX ??= $point;
                $activeAreaCount++;
            } else {
                $activeAreaCount--;
                if ($activeAreaCount == 0) {
                    $fieldCount += (floor($point) - $startX + 1);
                    $startX = null;
                }
            }
        }

        return $fieldCount - $this->getBeaconsAtLine($y);
    }

    public function result2(): int
    {
        for ($y = 0; $y <= 4000000; $y++) {
            $activeAreaCount = 0;
            $lastEnd = null;

            foreach ($this->getAreaDataForLine($y) as $point) {
                if (is_int($point)) {
                    if ($lastEnd && $lastEnd < $point - 1) {
                        return ($lastEnd + 1) * 4000000 + $y;
                    }
                    $activeAreaCount++;
                } else {
                    $activeAreaCount--;
                    if ($activeAreaCount == 0) {
                        $lastEnd = floor($point);
                    }
                }
            }
        }

        return 0;
    }

    public function getBeaconsAtLine(int $y): int
    {
        return $this->sensorsBeacons->where('by', $y)
            ->pluck('x')
            ->unique()  // multiple sensors can have the same beacon as closest but we want to count a beacon only once
            ->count();
    }

    // return an array of start and end points of the covered areas
    // for efficiency we store them as numbers where x.0 is start and x.1 is end.
    // this makes it easy to sort because we want starts first, to avoid thinking we are at the end of an area
    // while actually the next area is starting at the same time the previous one ends

    public function getAreaDataForLine(int $y): array
    {
        $points = [];

        foreach ($this->sensorsBeacons as $sensor) {
            $dy = abs($y - $sensor['sy']);
            $dx = $sensor['r'] - $dy;

            if ($dx < 0) {
                continue;
            }

            $points[] = (int) ($sensor['sx'] - $dx);
            $points[] = $sensor['sx'] + $dx + 0.1;
        }

        sort($points);

        return $points;
    }
}
