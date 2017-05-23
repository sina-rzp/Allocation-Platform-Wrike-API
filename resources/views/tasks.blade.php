@extends('layouts.master')

@section('content')

<div class="col-md-12">
<h1>{{ $title }}</h1>
@foreach ($tasks as $task)
<div class="well class-md-12">
	<a href="/api/type/tasks/{{ $task['id'] }}">{{ $task['title'] }}</a><br/>
</div>
@endforeach
<br><br>
<h3>New Tasks to add</h3>


@php
$unique = explode("]", $title, 2);
$unique = $unique[0];
$unique = substr($unique, 1);
@endphp

@foreach ($stored_tasks as $stored_task)
	<div class="col-md-12 well">[{{ $stored_task->unique_code }}] {{ $stored_task->title }} | {{ $stored_task->hours }}  hours | ({{ $stored_task->team->title }}) <a href="/api/create-task/folder/{{ $folder_id }}/new-task/{{ $stored_task->id }}/folder-code/{{ $unique }}"><button type="button" class="btn btn-success">Add</button></a></div><br/>
@endforeach
</div>
@endsection

