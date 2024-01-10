<?php

namespace App;

use Illuminate\Support\Collection;

class Day25 extends Day
{
    public static string $title = 'Full of Hot Air';

    protected Collection $balloons;

    protected array $translateToDec = [
        '=' => -2,
        '-' => -1,
        '0' => 0,
        '1' => 1,
        '2' => 2
    ];

    protected array $translateToSnafu;

    public function __construct()
    {
        parent::__construct();

        $this->translateToSnafu = array_flip($this->translateToDec);

//        $this->input = '1=-0-2
//12111
//2=0=
//21
//2=01
//111
//20012
//112
//1=-1=
//1-12
//12
//1=
//122';

        $this->balloons = str($this->input)->explode("\n");
    }

    public function result1(): string
    {
        $sums = $this->balloons->map(fn ($snafu) => $this->snafuToDec($snafu));

        return $this->decToSnafu($sums->sum());
    }

    public function result2(): int
    {
        return 1;
    }

    protected function snafuToDec(string $snafu): int
    {
        return str($snafu)
            ->split(1)
            ->map(fn ($digit) => $this->translateToDec[$digit])
            ->reverse()
            ->values()
            ->map(fn ($digit, $power) => $digit * pow(5, $power))
            ->sum();
    }

    protected function decToSnafu(int $number): string
    {
        $snafu = '';

        while ($number > 0) {
            $remainder = $number % 5;

            if ($remainder > 2) {
                $remainder -= 5;
                $number += 5;
            }

            $snafu = $this->translateToSnafu[$remainder] . $snafu;

            $number = floor($number / 5);
        }

        return $snafu;
    }
}
