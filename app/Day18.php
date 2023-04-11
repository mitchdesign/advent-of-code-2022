<?php

namespace App;

use Illuminate\Support\Collection;

class Day18 extends Day
{
    public static string $title = 'Boiling Boulders';

    protected Collection $droplets;
    protected array $dropletsCheck = [];
    protected array $axisExtremes = [];

    public function __construct()
    {
        parent::__construct();

//        $this->input = '2,2,2
//1,2,2
//3,2,2
//2,1,2
//2,3,2
//2,2,1
//2,2,3
//2,2,4
//2,2,6
//1,2,5
//3,2,5
//2,1,5
//2,3,5';

        $this->input = trim($this->input);

        $this->droplets = $this->scanDroplets();
        $this->axisExtremes = $this->getExtremes();

        $this->dropletsCheck = $this->droplets->map(fn ($droplet) => [
            "x{$droplet['x']}y{$droplet['y']}z{$droplet['z']}",
            "y{$droplet['y']}x{$droplet['x']}z{$droplet['z']}",
            "z{$droplet['z']}x{$droplet['x']}y{$droplet['y']}",
        ])->flatten()->toArray();
    }

    public function puzzle1(): int
    {
        return $this->countFreeFaces();
    }

    public function puzzle2(): int
    {
        return 1;
    }

    protected function countFreeFaces(): int
    {
        $count = 0;

        foreach (['x', 'y', 'z'] as $axis) {
            [$firstAxis, $secondAxis] = array_values(array_diff(['x', 'y', 'z'], [$axis]));

            $scanState = [];
            for ($firstCount = $this->axisExtremes[$firstAxis]['min']; $firstCount <= $this->axisExtremes[$firstAxis]['max'] + 1; $firstCount++) {
                for ($secondCount = $this->axisExtremes[$secondAxis]['min']; $secondCount <= $this->axisExtremes[$secondAxis]['max'] + 1; $secondCount++) {
                    $scanState[$firstCount][$secondCount] = false;
                }
            }

            for ($axisCount = $this->axisExtremes[$axis]['min']; $axisCount <= $this->axisExtremes[$axis]['max'] + 1; $axisCount++) {
                for ($firstCount = $this->axisExtremes[$firstAxis]['min']; $firstCount <= $this->axisExtremes[$firstAxis]['max'] + 1; $firstCount++) {
                    for ($secondCount = $this->axisExtremes[$secondAxis]['min']; $secondCount <= $this->axisExtremes[$secondAxis]['max'] + 1; $secondCount++) {
                        $isSet = in_array("{$axis}{$axisCount}{$firstAxis}{$firstCount}{$secondAxis}{$secondCount}", $this->dropletsCheck);
                        if ($isSet != $scanState[$firstCount][$secondCount]) {
                            $count++;
                            $scanState[$firstCount][$secondCount] = $isSet;
                        }
                    }
                }
            }
        }

        return $count;
    }

    protected function scanDroplets(): Collection
    {
        return collect(explode("\n", $this->input))->map(function ($line) {
            [$x, $y, $z] = explode(',', $line);
            return ['x' => $x, 'y' => $y, 'z' => $z];
        });
    }

    protected function getExtremes(): array
    {
        $extremes = [
            'x' => [
                'min' => $this->droplets->pluck('x')->min(),
                'max' => $this->droplets->pluck('x')->max(),
            ],
            'y' => [
                'min' => $this->droplets->pluck('y')->min(),
                'max' => $this->droplets->pluck('y')->max(),
            ],
            'z' => [
                'min' => $this->droplets->pluck('z')->min(),
                'max' => $this->droplets->pluck('z')->max(),
            ],
        ];

        return $extremes;
    }
}
