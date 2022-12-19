<?php

namespace App\Models\Day7;

class Dir
{
    public ?Dir $parent = null;
    public array $dirs = [];
    public array $files = [];
    public string $name = '';
    public string $path = '';
    public int $totalSize = 0;

    public function __construct(string $name, ?Dir $parent)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->path = $parent
            ? $this->parent->path . '/' . $name
            : $name;
    }

    public function addDirIfNotExist(string $name): ?Dir
    {
        if (! in_array($name, $this->dirs)) {
            return $this->addDir($name);
        }

        return null;
    }

    public function addDir(string $name): Dir
    {
        $newDir = new Dir($name, $this);
        $this->dirs[$name] = $newDir;
        return $newDir;
    }

    public function addFile(string $name, int $size): File
    {
        $newFile = new File($name, $this, $size);
        $this->files[$name] = $newFile;
        $this->addSize($size);
        return $newFile;
    }

    public function addSize($size): void
    {
        $this->totalSize += $size;
        $this->parent?->addSize($size);
    }
}
