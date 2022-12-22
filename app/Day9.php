<?php

namespace App;

use App\Models\Day9\Position;
use Illuminate\Support\Collection;

class Day9 extends Day
{
    public static string $title = 'Rope Bridge';

    private Collection $rope;
    private Position $head;
    private Position $tail;

    private array $visBounds;
    private Collection $tailPositionsVisited;

    public function __construct()
    {
        parent::__construct();

        $this->visBounds = ['x' => [-10, 10], 'y' => [-10, 10]];

        $this->tailPositionsVisited = collect();
    }

    private function initRope(int $length)
    {
        $this->rope = Collection::times($length, fn() => new Position);

        for ($i = 1; $i < $this->rope->count(); $i++) {
            $this->rope->get($i)->setFollows($this->rope->get($i - 1));
        }

        $this->head = $this->rope->first();
        $this->tail = $this->rope->last();
    }

    public function result1(): int
    {
        $this->initRope(2);
        $this->followMoves();
        $this->visualize();

        return $this->tailPositionsVisited->unique()->count();
    }

    public function result2(): int
    {
        $this->initRope(10);
        $this->followMoves();
        $this->visualize();

        return $this->tailPositionsVisited->unique()->count();
    }

    private function followMoves(): void
    {
        str($this->input)->trim()
            ->explode("\n")
            ->each(function ($move) {
                [$direction, $amount] = explode(' ', $move);
                for ($i = 0; $i < $amount; $i++) {
                    $this->head->move($direction);
                    $this->rope->each(fn ($node) => $node->follow());
                    $this->logTailPosition($this->tail);
                }
            });
    }

    private function logTailPosition(Position $tail): void
    {
        $x = $tail->x;
        $y = $tail->y;

        $this->tailPositionsVisited->push((string) $tail);

        $this->visBounds['x'][0] = min($this->visBounds['x'][0], $x - 10);
        $this->visBounds['x'][1] = max($this->visBounds['x'][1], $x + 10);
        $this->visBounds['y'][0] = min($this->visBounds['y'][0], $y - 10);
        $this->visBounds['y'][1] = max($this->visBounds['y'][1], $y + 10);
    }

    private function visualize()
    {
        $vis = array_fill_keys(range(...$this->visBounds['y']), array_fill_keys(range(...$this->visBounds['x']), '.'));
        $vis[0][0] = 's';

        $this->tailPositionsVisited->each(function ($pos) use (&$vis) {
           [$x, $y] = explode('|', $pos);
           $vis[$y][$x] = '#';
        });

        $this->rope->reverse()->each(function ($node, $key) use (&$vis) {
            $symbol = match ($key) {
                0 => 'H',
                default => $key
            };
            $vis[$node->y][$node->x] = $symbol;
        });

        echo '<pre style="font-size: 0.5rem;">' . join("\n", array_map(fn ($row) => join('', $row), $vis)) . '</pre>';
    }
}
