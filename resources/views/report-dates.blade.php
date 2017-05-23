@extends('layouts.master')

@section('content')

<div class="col-md-12">
<h1>{{ $title }}</h1>


<h3>Choose a date:</h3>

<form method="POST" action="#" class="allocation-form form">
	<div class="form-group">
		<label for="start">Start:</label>
		<input type="text" class="form-control" placeholder="2017-01-01" id="start" name="start">
		<span id="helpBlock1" class="help-block">Format: 2017-01-01</span>
	</div>
	<div class="form-group">
		<label for="end">End:</label>
		<input type="text" class="form-control" placeholder="2017-01-31" id="end" name="end">
		<span id="helpBlock2" class="help-block">Format: 2017-01-31</span>
	</div>
	<input type="submit" class="btn btn-default" name="Submit" value="Submit">
</form>

</div>
<script type="text/javascript">
	$('.allocation-form').submit(function(e){
		e.preventDefault();
		start = $('#start').val();
		end = $('#end').val();
		url = '/api/report/start/'+start+'/end/'+end;

		$.ajax({
	        type: "GET",
	        url: url,
	        success : function(data) {
	            console.log("there are "+data+" tasks!");
	        }
	    });


		// window.location = url;
	});
</script>
@endsection

