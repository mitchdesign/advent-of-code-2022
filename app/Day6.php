<?php

namespace App;

class Day6 extends Day
{
    public static string $title = 'Tuning Trouble';

    public int $bufferSize = 0;
    public string $buffer = '';
    public int $counter = 0;

    public function __construct()
    {
        parent::__construct();

        $this->input = trim($this->input); // to be sure we count buffers correctly
    }

    public function result1(): int
    {
        return $this->findUniqueBufferOfLength(4);
    }

    public function result2(): int
    {
        return $this->findUniqueBufferOfLength(14);
    }

    private function findUniqueBufferOfLength(int $length)
    {
        $this->bufferSize = $length;

        do {
            $this->feedCharacter();
        } while (! $this->bufferIsMarker());

        return $this->counter;
    }

    private function feedCharacter(): void
    {
        $char = substr($this->input, 0, 1);
        $this->input = substr($this->input, 1);
        $this->buffer = substr($this->buffer, ($this->bufferSize * -1) + 1) . $char;
        $this->counter++;
    }

    private function bufferIsMarker(): bool
    {
        return $this->counter >= $this->bufferSize // dont find a unique buffer before it is filled up
            && count(array_unique(str_split($this->buffer, 1))) === $this->bufferSize;
    }
}
