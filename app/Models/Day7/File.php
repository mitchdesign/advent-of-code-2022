<?php

namespace App\Models\Day7;

class File
{
    public Dir $parent;
    public string $name = '';
    public int $size = 0;

    public function __construct(string $name, Dir $parent, int $size)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->size = $size;
    }
}
