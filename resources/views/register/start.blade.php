@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('/') }}">Back to Login</a>
<div class="row col-md-12">
	<h2 class="register-heading">I am a(n)...</h2>
</div>

<div class="row">
	<div class="col-md-4">
		<a href="{{ URL::to('register/type') }}" class="register-choice">
		New member<br><span>(I have never applied before)</span>
		</a>
	</div>
	<div class="col-md-4">
		<a href="{{ URL::to('register/reapply') }}" class="register-choice">
		Existing member<br><span>(I am reapplying for membership after being denied or banned)</span>
		</a>
	</div>
</div>
@stop
