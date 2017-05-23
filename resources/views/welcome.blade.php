@extends('layouts.master')

@section('content')

@if ($status == 0)
<div class="alert alert-success" role="alert">Logged In</div>
@else
<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>
  Please log-in!
</div>
@endif

@if (!empty($message))
<div class="alert alert-danger" role="alert">
  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
  <span class="sr-only">Error:</span>
  {{ $message }}
</div>
@endif

<div class="well class-md-12">
@php
$client_id = env('CLIENT_ID');
@endphp
<a href="https://www.wrike.com/oauth2/authorize?client_id={{ $client_id }}&response_type=code">Start</a>
</div>
<div class="well class-md-12">
<a href="/api/calculate-tasks">Report</a>
</div>
@endsection

{{-- Footer is included after this --}}