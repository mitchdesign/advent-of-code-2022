<?php

namespace App;

class Day20 extends Day
{
    public static string $title = 'Grove Positioning System';

    private array $inputCode;
    private array $order;
    private int $count;

    private int $key = 811589153;

    public function __construct()
    {
        parent::__construct();

//        $this->input = '1
//2
//-3
//3
//-2
//0
//4';

        $this->inputCode = str($this->input)
            ->trim()
            ->split("/\n/")
            ->map(fn ($str) => (int) $str)
            ->values()
            ->toArray();

        $this->count = count($this->inputCode);

        $this->order = array_keys($this->inputCode);
    }

    public function puzzle1(): int
    {
        $this->mix();

        return $this->answer();
    }

    public function puzzle2(): int
    {
        // multiply each input with the key as described
        $this->inputCode = array_map(fn ($number) => $number * $this->key, $this->inputCode);

        $this->mix(10);

        return $this->answer();
    }

    private function mix(int $times = 1): void
    {
        $count = 0;
        while ($count++ < $times) {
            foreach ($this->inputCode as $key => $value) {
                // find the current location of this value,
                // after finding the number by its index (as numbers can occur multiple times)
                $position = array_search($key, $this->order);
                array_splice($this->order, $position, 1);

                // now we will shift. we only need to shift by the remainder of the large value
                // devided by the size of the array of numbers (which has the one we are moving removed already)
                $countWhileShifting = $this->count - 1;
                $newPosition = $position + $value % $countWhileShifting;

                // we could come out outside bounds, so maybe shift one more time to get back into bounds
                if ($newPosition >= $countWhileShifting) {
                    $newPosition -= $countWhileShifting;
                }
                while ($newPosition < 0) {
                    $newPosition += $countWhileShifting;
                }

                array_splice($this->order, $newPosition, 0, [$key]);
            }
        }
    }

    public function answer(): int
    {
        $zeroIndex = array_search(0, $this->inputCode);
        $zeroPos = array_search($zeroIndex, $this->order);

        $sum = 0;

        foreach ([1000, 2000, 3000] as $p) {
            $index = ($zeroPos + $p) % $this->count;
            $sum += $this->inputCode[$this->order[$index]];
        }

        return $sum;
    }
}
