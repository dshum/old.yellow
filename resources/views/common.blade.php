@extends('layout')

@section('title')
{{ $currentElement->title }}
@stop

@section('content')
<h2><span>{{ $currentElement->h1 }}</span></h2>
<div class="info">
	<p>{!! $currentElement->fullcontent !!}</p>
</div>
@stop