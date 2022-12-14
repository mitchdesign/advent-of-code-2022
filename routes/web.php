<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $puzzles = collect(range(1,25))
        ->filter(fn ($i) => class_exists("\App\Day{$i}"))
        ->map(fn ($i) => [
            'day' => $i,
            'title' => ("\App\Day{$i}")::$title
        ]);

    return view('index')
        ->with('puzzles', $puzzles);
});

Route::get('/{day}/{puzzle}', function ($day, $puzzle) {
    $class = "App\Day{$day}";
    $method = "puzzle{$puzzle}";

    return (new $class)->$method();
});
