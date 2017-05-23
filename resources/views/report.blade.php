@extends('layouts.master')

@section('content')

<div class="col-md-12">
<h1>Report</h1>
<table class="table table-bordered">
	<tbody>
		<tr>
			<th>Title</th>
			<th>Allocated</th>
			<th>Spent</th>
			<th>Balance</th>
			<th>Created Time</th>

		</tr>
		@foreach ($values as $value)
		<tr>
			<td>{{ $value['title'] }}</td>
			<td>{{ $value['allocated'] }}</td>
			<td>{{ $value['logs'] }}</td>
			<td>{{ $value['balance'] }}</td>
			<td>{{ $value['createdDate'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

</div>


@endsection

