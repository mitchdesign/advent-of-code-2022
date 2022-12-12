<html>
<body>

<h1>Advent of Code 2022</h1>

@foreach ($puzzles as $puzzle)
    <p>
        <a href="{{ $puzzle }}/1">Day {{ $puzzle }} puzzle 1</a>
        <a href="{{ $puzzle }}/2">Day {{ $puzzle }} puzzle 2</a>
    </p>
@endforeach

</body>
</html>
