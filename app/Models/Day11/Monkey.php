<?php

namespace App\Models\Day11;

use Illuminate\Support\Collection;

class Monkey
{
    // example definition:
/*
Monkey 2:
Starting items: 79, 60, 97
Operation: new = old * old
Test: divisible by 13
If true: throw to monkey 1
If false: throw to monkey 3
*/

    public int $id;
    private array $items;
    private string $operation;
    private int|string $operationValue;
    public int $divisibleTest;
    public self|int $monkeyIfTrue;
    public self|int $monkeyIfFalse;
    public int $inspections = 0;
    public int $modulo;

    public function __construct(string $definition)
    {
        $pattern = '/Monkey (?<id>\d):
  Starting items: (?<items>[\d, ]+)
  Operation: new = old (?<operation>[\*\+]) (?<operationValue>\w+)
  Test: divisible by (?<divisibleTest>\d+)
    If true: throw to monkey (?<monkeyIfTrue>\d)
    If false: throw to monkey (?<monkeyIfFalse>\d)/';

        if (! preg_match($pattern, $definition, $matches)) {
            throw new \Exception('Definition not matched');
        }

        $this->id = (int) $matches['id'];
        $this->items = str($matches['items'])->explode(',')->map(fn ($items) => str($items)->trim()->toInteger())->toArray();
        $this->operation = $matches['operation'];
        $this->operationValue = $matches['operationValue'] == 'old' ? 'old' : (int) $matches['operationValue'];
        $this->divisibleTest = (int) $matches['divisibleTest'];
        $this->monkeyIfTrue = (int) $matches['monkeyIfTrue'];
        $this->monkeyIfFalse = (int) $matches['monkeyIfFalse'];
    }

    public function go(bool $reduceWorry = false, bool $dump = false): void
    {
        if ($dump) { echo("\n--- Monkey $this->id goes.\n"); }
        foreach ($this->items as $item) {
            if ($dump) { echo("Examines Item $item\n"); }
            $value = $this->operationValue === 'old' ? $item : $this->operationValue;
            $value = match ($this->operation) {
                '*' => $item * $value,
                '+' => $item + $value,
            };
            $this->inspections++;
            if ($dump) { echo("Increase value by $this->operation $this->operationValue to $value\n"); }
            if ($reduceWorry) {
                $value = floor($value / 3);
                if ($dump) { echo("Devide by 3 to $value\n"); }
            } else {
                if ($value > $this->modulo) {
                    $value = $value % $this->modulo;
                    if ($dump) { echo "Reduce safely to $value\n"; }
                }
            }
            $monkey = ($test = ($value % $this->divisibleTest == 0))
                ? $this->monkeyIfTrue
                : $this->monkeyIfFalse;
            if ($dump) { echo("Test devisible by $this->divisibleTest : " . ($test ? 'true' : 'false')) ."\n"; }
            if ($dump) { echo("Throw to monkey $monkey->id\n"); }
            $monkey->receive($value);
        }
        $this->items = [];
    }

    public function receive($item): void
    {
        $this->items[] = $item;
    }

    public function getItems(): Collection
    {
        return collect($this->items);
    }
}
