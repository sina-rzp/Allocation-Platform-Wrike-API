@extends('layouts.master')

@section('content')

<div class="col-md-12">
<h1>{{ $title }}</h1>
@foreach ($folders as $folder)
<div class="well class-md-12">
	<a href="/api/type/folders/id/{{ $folder['id'] }}">{{ $folder['title'] }}</a><br/>
</div>
@endforeach
</div>

@endsection

