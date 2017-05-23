@extends('layouts.master')

@section('content')

<h1>{{ $title }}</h1>

<p>Allocated: {{ $allocated }}</p>
<p>Spent: {{ $sum }}</p>
<p>Balance: {{ $balance }}</p>
<p>Created: {{ $created_date }}</p>


<form method="POST" action="#" class="allocation-form">
	<input type="hidden" name="id" id="task-id" value="{{ $id }}">
	<input type="text" id="task-amount" name="amount">
	<input type="submit" name="Submit" value="Submit">
</form>

<script type="text/javascript">
	$('.allocation-form').submit(function(e){
		e.preventDefault();
		id = $('#task-id').val();
		amount = $('#task-amount').val();
		url = '/api/type/tasks/'+id+'/modify/'+amount;
		window.location = url;
	});
</script>
@endsection

