<?php

namespace App;

class Day21 extends Day
{
    public static string $title = 'Monkey Math';

    protected array $monkeys;

    protected bool $sideCheck = false;

    public function __construct()
    {
        parent::__construct();

//        $this->input = 'root: pppw + sjmn
//dbpl: 5
//cczh: sllz + lgvd
//zczc: 2
//ptdq: humn - dvpt
//dvpt: 3
//lfqf: 4
//humn: 5
//ljgn: 2
//sjmn: drzm * dbpl
//sllz: 4
//pppw: cczh / lfqf
//lgvd: ljgn * ptdq
//drzm: hmdt - zczc
//hmdt: 32';

        $this->monkeys = str($this->input)
            ->trim()
            ->split("/\n/")
            ->mapWithKeys(function ($line) {
                $line = str($line);
                $monkey = $line->before(':')->toString();
                $formula = $line->after(': ')->split('/ /');

                if (count($formula) == 1) {
                    $formula = (int) $formula[0];
                }

                return [$monkey => $formula];
            })->toArray();
    }

    public function puzzle1(): int
    {
        return $this->getMonkeyValue('root');
    }

    public function puzzle2(): int
    {
        [$parent, $parentValue] = $this->calculateSideValue('root');

        // we have the parent and value. we will check the 2 sides, find the value of the one we can calculate,
        // and based on the parent definition we will check what must be the value of the other side

        do {
            [$side1, $operator, $side2] = $this->monkeys[$parent];
            [$side, $otherSideValue] = $this->calculateSideValue($parent);

            switch ($operator) {
                case '*':
                    $humanSideValue = $parentValue / $otherSideValue;
                    break;
                case '+':
                    $humanSideValue = $parentValue - $otherSideValue;
                    break;
                case '-':
                    $humanSideValue = $side == $side1
                        ? $parentValue + $otherSideValue
                        : $otherSideValue - $parentValue;
                    break;
                case '/':
                    $humanSideValue = $side == $side1
                        ? $parentValue * $otherSideValue
                        : $otherSideValue / $parentValue;
                    break;
            }

            $parent = $side;
            $parentValue = $humanSideValue;

        } while ($side != 'humn');

        return $humanSideValue;
    }

    protected function calculateSideValue(string $monkey): array
    {
        $this->sideCheck = true;

        [$side1, , $side2] = $this->monkeys[$monkey];

        try {
            $value1 = $this->getMonkeyValue($side1);
        } catch (\Exception $e) {
            $value1 = null;
        }

        try {
            $value2 = $this->getMonkeyValue($side2);
        } catch (\Exception $e) {
            $value2 = null;
        }

        return is_null($value1)
            ? [$side1, $value2]
            : [$side2, $value1];
    }

    protected function getMonkeyValue(string $monkey): int
    {
        if ($this->sideCheck && $monkey == 'humn') {
            throw new \Exception('Found it');
        }

        $value = $this->monkeys[$monkey];

        if (is_numeric($value)) {
            return $value;
        }

        [$monkey1, $operator, $monkey2] = $value;

        $monkey1 = $this->getMonkeyValue($monkey1);
        $monkey2 = $this->getMonkeyValue($monkey2);

        return match ($operator) {
            '+' => $monkey1 + $monkey2,
            '-' => $monkey1 - $monkey2,
            '*' => $monkey1 * $monkey2,
            '/' => $monkey1 / $monkey2,
        };
    }


}
