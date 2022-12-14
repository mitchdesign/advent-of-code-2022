<html>
<body>

<h1>Advent of Code 2022</h1>

<table>
@foreach ($puzzles as $puzzle)
    <tr>
        <td>Day {{ $puzzle['day'] }}</td>
        <td>{{ $puzzle['title'] }}</td>
        <td><a href="{{ $puzzle['day'] }}/1">Puzzle 1</a></td>
        <td><a href="{{ $puzzle['day'] }}/2">Puzzle 2</a></td>
    </tr>
@endforeach
</table>

</body>
</html>
