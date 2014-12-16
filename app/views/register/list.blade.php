@extends('layouts.main')
@section('content')
<div class="register">
	<h2 class="register-heading">Are you a(n)...</h2>
	<a href="{{ URL::to('register/goon') }}" class="register-choice">
	Goon<br><span>(or being sponsored by one)</span>
	</a>
	<a href="{{ URL::to('register/affiliate') }}" class="register-choice">
	Affiliate<br><span>(a member of an affiliated organization)</span>
	</a>
</div>
@stop
