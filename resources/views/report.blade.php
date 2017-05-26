
@foreach ($values as $value)
<tr>
	<td>{{ $value['number'] }}</td>
	<td>{{ $value['title'] }}</td>
	<td>{{ $value['allocated'] }}</td>
	<td>{{ $value['logs'] }}</td>
	<td>{{ $value['balance'] }}</td>
	<td>{{ $value['createdDate'] }}</td>
</tr>
@endforeach
