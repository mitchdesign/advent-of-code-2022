<?php

namespace App;

use Illuminate\Support\Collection;

class Day10 extends Day
{
    public static string $title = 'Cathode-Ray Tube';

    private int $x = 1;
    private int $cycle = 0;

    private array $interestingCycles = [20, 60, 100, 140, 180, 220];
    private Collection $signal;

    public function __construct()
    {
        parent::__construct();
        $this->signal = collect();
    }

    public function result1(): int
    {
        $this->runTask(function () {
            if (in_array($this->cycle, $this->interestingCycles)) {
                $this->signal->put($this->cycle, $this->x);
            }
        });

        return $this->signal->map(fn ($x, $cycle) => $x * $cycle)->sum();
    }

    public function result2(): string
    {
        $this->runTask(function () {
            $pixelPosition = $this->cycle % 40; // pixel/cycle is 1 indexed
            $spritePosition = $this->x + 1;     // x is 0 indexed

            $this->signal->add(abs($pixelPosition - $spritePosition) <= 1 ? '#' : '.');
        });

        return $this->signal->chunk(40)
            ->map(fn ($chunk) => $chunk->join(''))
            ->join("\n");
    }

    private function parseCommand(string $command): array
    {
        $command = str($command);

        if ($command->is('noop')) {
            return [
                'cycles' => 1,
                'add' => 0,
            ];
        }

        if ($command->startsWith('addx')) {
            return [
                'cycles' => 2,
                'add' => $command->after('addx ')->toInteger(),
            ];
        }

        throw new \Exception('Unrecognized command');
    }

    private function runTask(\Closure $task)
    {
        str($this->input)
            ->explode("\n")
            ->map(fn ($line) => trim($line))
            ->filter()
            ->each(function ($command) use ($task) {
                $command = $this->parseCommand($command);

                for ($i = 1; $i <= $command['cycles']; $i++) {
                    $this->cycle++;
                    $task();
                }

                $this->x += $command['add'];
            });
    }
}
