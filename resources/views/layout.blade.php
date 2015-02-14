<!DOCTYPE html>
<html>
<head>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>@yield('title')</title>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<link href="{{ asset('css/default.css') }}" rel="stylesheet">
</head>
<body>
	<div class="left">
		<h1>@if ($currentRouteName != 'welcome')<a href="{{ route('welcome') }}">Магазин</a>@else<h1><span>Магазин</span></h1>@endif</h1>
@if ($loggedUser)
		<p><a href="{{ URL::route('cabinet') }}">{{ $loggedUser->email }}</a></p>
		<p><a href="{{ URL::route('logout') }}">Выход</a></p>
@else
		<p><a href="{{ URL::route('login') }}">Войти</a></p>
		<p><a href="{{ URL::route('register') }}">Зарегистрироваться</a></p>
@endif
		<p><a href="{{ URL::route('cart') }}">Корзина</a></p>
		<p><a href="{{ URL::route('order') }}">Оформить заказ</a></p>
		<ul>
			<li><a href="{{ URL::route('novelty') }}">Новинки</a></li>
			<li><a href="{{ URL::route('special') }}">Спецпредложения</a></li>
			<li><a href="{{ URL::route('delivery') }}">Доставка</a></li>
			<li><a href="{{ URL::route('payments') }}">Способы оплаты</a></li>
			<li><a href="{{ URL::route('contacts') }}">Контакты</a></li>
		</ul>
@foreach ($categoryList as $k => $category)
		<p><a href="{{ $category->getHref() }}">{{ $category->name }}</a></p>
@endforeach
	</div>
	<div class="center">
@yield('content')
	</div>
	<br clear="both" />
	<div class="footer">
<?php $queries = DB::getQueryLog();?>
		<ol>
@foreach ($queries as $query)
			<li>{{ $query['time'] / 1000 }} sec. {{ $query['query'] }}</li>
@endforeach
		</ol>
	</div>
</body>
</html>