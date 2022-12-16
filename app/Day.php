<?php

namespace App;

use Illuminate\Support\Facades\Storage;

class Day
{
    protected int $day = 0;
    protected string $input = '';

    public function __construct()
    {
        $this->day = (int) preg_replace('/\D/','', get_class($this));
        $file = "input{$this->day}.txt";

        if (Storage::exists($file)) {
            $this->input = Storage::get($file);
        }
    }

    public function puzzle1()
    {
        return view('puzzle')
            ->with('day', $this->day)
            ->with('puzzle', 1)
            ->with('result', $this->result1());
    }

    public function puzzle2()
    {
        return view('puzzle')
            ->with('day', $this->day)
            ->with('puzzle', 2)
            ->with('result', $this->result2());
    }
}
