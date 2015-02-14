@extends('layout')

@section('title')
Фрукты и овощи | Yellow App
@stop

@section('content')
@foreach ($categoryList as $k => $category)
	<div class="category">
	@if ($category->image)
	<a href="{{ $category->getHref() }}"><img src="{{ $category->getProperty('image')->src() }}" width="{{ $category->getProperty('image')->width() }}" height="{{ $category->getProperty('image')->height() }}" /></a><br />
	@endif
	<a href="{{ $category->getHref() }}">{{ $category->name }}</a>
	</div>
@endforeach
@stop