<?php

namespace App;

use App\Models\Day9\Position;
use Illuminate\Support\Collection;

class Day9 extends Day
{
    public static string $title = 'Rope Bridge';

    private Position $head;
    private Position $tail;

    private array $visBounds;
    private Collection $tailPositionsVisited;

    public function __construct()
    {
        parent::__construct();

        $this->head = new Position;
        $this->tail = new Position;

        $this->visBounds = ['x' => [-10, 10], 'y' => [-10, 10]];

        $this->tailPositionsVisited = collect();
    }

    public function result1(): int
    {
        str($this->input)->trim()
            ->explode("\n")
            ->each(function ($move) {
                [$direction, $amount] = explode(' ', $move);
                for ($i = 0; $i < $amount; $i++) {
                    $this->head->move($direction);
                    $this->tail->follow($this->head);
                    $this->logTailPosition($this->tail);
                }
            });

        $this->visualize();

        return $this->tailPositionsVisited->unique()->count();
    }

    public function result2(): int
    {
        return 1;
    }

    private function logTailPosition(Position $tail): void
    {
        $x = $tail->x;
        $y = $tail->y;

        $this->tailPositionsVisited->push((string) $tail);

        $this->visBounds['x'][0] = min($this->visBounds['x'][0], $x - 5);
        $this->visBounds['x'][1] = max($this->visBounds['x'][1], $x + 5);
        $this->visBounds['y'][0] = min($this->visBounds['y'][0], $y - 5);
        $this->visBounds['y'][1] = max($this->visBounds['y'][1], $y + 5);
    }

    private function visualize()
    {
        $vis = array_fill_keys(range(...$this->visBounds['y']), array_fill_keys(range(...$this->visBounds['x']), '.'));
        $vis[0][0] = 's';

        $this->tailPositionsVisited->each(function ($pos) use (&$vis) {
           [$x, $y] = explode('|', $pos);
           $vis[$y][$x] = '#';
        });

        $vis[$this->tail->y][$this->tail->x] = 'T';
        $vis[$this->head->y][$this->head->x] = 'H';

        echo '<pre style="font-size: 0.5rem;">' . join("\n", array_map(fn ($row) => join('', $row), $vis)) . '</pre>';
    }
}
