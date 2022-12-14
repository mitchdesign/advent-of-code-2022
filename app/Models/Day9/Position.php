<?php

namespace App\Models\Day9;

class Position implements \Stringable
{
    public int $x = 0;
    public int $y = 0;

    private ?Position $previous = null;

    public function setFollows(Position $previous)
    {
        $this->previous = $previous;
    }

    public function move(string $direction): void
    {
        switch ($direction) {
            case 'U':
                $this->y--;
                break;
            case 'D':
                $this->y++;
                break;
            case 'L':
                $this->x--;
                break;
            case 'R':
                $this->x++;
                break;
        }
    }

    public function add(int $x, int $y): void
    {
        $this->x += $x;
        $this->y += $y;
    }

    public function diff(self $position)
    {
        return [$this->x - $position->x, $this->y - $position->y];
    }

    public function follow(): void
    {
        if (! $this->previous) {
            return;
        }

        [$dx, $dy] = $this->diff($this->previous);

        if (abs($dx) >= 2) {
            // move 1 steps in the right direction and compensate for any y deviation
            // meaning "fall in line" behind the head
            $x = ($dx < 0) ? 1 : -1;
            $y = $dy == 0
                ? 0
                : ($dy < 0 ? 1 : -1);
            $this->add($x, $y);
        } elseif (abs($dy) >= 2) {
            // move 1 steps in the right direction and compensate for any x deviation
            // meaning "fall in line" behind the head
            $y = ($dy < 0) ? 1 : -1;
            $x = $dx == 0
                ? 0
                : ($dx < 0 ? 1 : -1);
            $this->add($x, $y);
        }
    }

    public function __toString(): string
    {
        return "{$this->x}|{$this->y}";
    }
}
