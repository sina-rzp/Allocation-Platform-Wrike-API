@extends('layouts.master')

@section('content')

<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

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

<br><br>

<div class="time"></div>


<div class="col-md-12 final-results">

<h1>Report</h1>

<table class="table table-bordered" id="myTable">
	<thead>
		<tr>
			<th>Number</th>
			<th>Title</th>
			<th>Allocated</th>
			<th>Spent</th>
			<th>Balance</th>
			<th>Created Time</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>

</div>




</div>
<script type="text/javascript">
	$('.allocation-form').submit(function(e){
		e.preventDefault();
		start = $('#start').val();
		end = $('#end').val();
		url = '/api/report/start/'+start+'/end/'+end;

		$.ajax({
	        type: "GET",
	        url: url+'/initial',
	        success : function(data) {

	        	$.when(
	        		
					$.get(url+"/counter/0/token/", function(html) {
						console.log("done1");
						table1 = html;
					}),
					$.get(url+"/counter/1/token/", function(html2) {
						console.log("done2");
						table2 = html2;
					}),
					$.get(url+"/counter/2/token/", function(html3) {
						console.log("done3");
						table3 = html3;
					})

				).then(function(new_data) {

					$(".table").append(table1);
					$(".table").append(table2);
					$(".table").append(table3);
					$(document).ready(function(){
						$('#myTable').DataTable();
					});
					$('.final-results').fadeIn();


				});

	        	time_taken = fancyTimeFormat(Math.round(data*2.5));

	            $(".time").text(data+" records found! It should take approximately: "+time_taken+". Please wait...");
	        }, 
	        error: function(data)
	        {
	        	window.location = "/welcome";
	        }
	    });

	});

	function fancyTimeFormat(time)
	{   
	    // Hours, minutes and seconds
	    var hrs = ~~(time / 3600);
	    var mins = ~~((time % 3600) / 60);
	    var secs = time % 60;

	    // Output like "1:01" or "4:03:59" or "123:03:59"
	    var ret = "";

	    if (hrs > 0) {
	        ret += "" + hrs + ":" + (mins < 10 ? "0" : "");
	    }

	    ret += "" + mins + ":" + (secs < 10 ? "0" : "");
	    ret += "" + secs;
	    return ret;
	}


</script>

<style type="text/css">
	.final-results
	{
		display: none;
	}
</style>
@endsection

