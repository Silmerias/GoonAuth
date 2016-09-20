@extends('layouts.main')
@section('content')

<?php
use App\Sponsor;
use App\User;
?>

<?php
$code = Sponsor::where('UID', $auth->UID)->whereNull('SSponsoredID')->first();
?>

<h1>Sponsor a Friend</h1>
<div class="row">
	<div class="col-md-12">
	<p>Sponsoring a friend is easy assuming you/him are not complete idiots! Just follow the steps below to get them authed.</p>
	<ol>
		<li>Generate a sponsor code below.</li>
		<li>Have your friend <a href="{{ URL::to('register/sponsored') }}">register as a sponsored member</a>.</li>
		<li>Give your friend an unused sponsor code so they can use it.</li>
	</ol>
	<p>That's all you have to do!</p>
	<p><strong>Remember! You are completely responsible for the actions of your friend. If the guy you sponsor fucks up, be prepared to take responsibility for their actions.</strong></p>
	@if (Session::has('error'))
	<div class="alert alert-danger">
		{{ Session::get('error') }}
	</div>
	@endif
	<h3>Your Sponsor Code</h3>

	@if (empty($code))
	<form action="{{ URL::to('sponsor/add') }}" method="post" class="form">
		{{ csrf_field() }}
		<button type="submit" class="btn btn-primary">Generate Code</button>
	</form>
	@else
	<kbd>{{ $code->SCode }}</kbd>
	@endif
	</div>
</div>
@stop
