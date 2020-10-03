<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Crawler</title>
	<link rel="stylesheet" href="">
</head>
<body>
	<form method="POST" action="/confianca">
		{{ csrf_field() }}
		<label for="pesquisa">Termo para Pesquisa</label>
		<input id="pesquisa" type="text" name="q" value="{{ old('q') }}">
		<button type="submit">Gerar</button>
	</form>
	@if(session('data'))
	<table border="1">
		<thead>
			<tr>
				<th>Link</th>
			</tr>
		</thead>
		<tbody>
			@foreach(session('data') as $link)
			<tr>
				<td>{{ $link }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	@endif
</body>
</html>