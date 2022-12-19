<?php

namespace App;

use App\Models\Day7\Dir;
use App\Models\Day7\File;
use Illuminate\Support\Collection;

class Day7 extends Day
{
    public static string $title = 'No Space Left On Device';

    public Dir $root;
    public Dir $currentDir;
    public Collection $allDirs;

    public function __construct()
    {
        parent::__construct();

        $this->root = new Dir('/', null);
        $this->allDirs = collect(['/' => $this->root]);

        $commands = explode("\n\$ ", "\n" . $this->input);

        foreach ($commands as $command) {
            $command = str($command)->trim();

            if ($command->isEmpty()) {
                continue;
            }

            if ($command->startsWith('cd ')) {
                $this->handleCd($command->after('cd ')->trim());
                continue;
            }

            if ($command->startsWith('ls')) {
                $this->handleLs($command->after('ls')->trim());
                continue;
            }

            throw new \RuntimeException('Unknown command found : ' . $command);
        }
    }

    private function handleCd(string $dir): void
    {
        switch ($dir) {
            case '..':
                $this->currentDir = $this->currentDir->parent;
            break;

            case '/':
                $this->currentDir = $this->root;
            break;

            default:
                if ($newDir = $this->currentDir->addDirIfNotExist($dir))
                {
                    $this->allDirs->put($newDir->path, $newDir);
                }

                $this->currentDir = $this->currentDir->dirs[$dir];
            break;
        }
    }

    private function handleLs(string $data): void
    {
        foreach (explode("\n", $data) as $item) {
            $item = str($item);
            if ($item->startsWith('dir ')) {
                continue;
            }

            [$size, $name] = $item->explode(' ');
            $this->currentDir->addFile($name, $size);
        }
    }

    public function result1(): int
    {
        return $this->allDirs
            ->filter(fn ($dir) => $dir->totalSize <= 100000)
            ->map(fn ($dir) => $dir->totalSize)
            ->sum();
    }

    public function result2(): int
    {
        $disk = 70000000;
        $needed = 30000000;
        $maxUse = $disk - $needed;
        $inUse = $this->allDirs->get('/')->totalSize;
        $toDelete = $inUse - $maxUse;

        return $this->allDirs
            ->filter(fn ($dir) => $dir->totalSize >= $toDelete)
            ->map(fn ($dir) => $dir->totalSize)
            ->sort()
            ->first();
    }
}
