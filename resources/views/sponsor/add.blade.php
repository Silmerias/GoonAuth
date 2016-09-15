@extends('layouts.main')
@section('content')

<?php
use App\Sponsor;
use App\User;
?>

<h1>Sponsor a Friend</h1>
<div class="row">
	<div class="col-md-12">
	<p>Sponsoring a friend is easy assuming you/him are not complete idiots! Just follow the steps below to get them authed.</p>
	<ol>
		<li>Generate a sponsor code below.</li>
		<li>Have your friend <a href="{{ URL::to('register/goon-sponsored') }}">register as a sponsored goon</a>.</li>
		<li>Give your friend an unused sponsor code so they can use it.</li>
	</ol>
	<p>That's all you have to do!</p>
	<p><strong>Remember! You are completely responsible for the actions of your friend. If the guy you sponsor fucks up, be prepared to take responsibility for their actions.</strong></p>
	@if (Session::has('error'))
	<div class="alert alert-danger">
		{{ Session::get('error') }}
	</div>
	@endif
	<h2>Available codes</h2>
	<table class="table">
		<thead>
			<th>Code</th>
		</thead>
		@foreach (Sponsor::where('UID', $auth->UID)->whereNull('SSponsoredID')->get() as $sponsor)
		<tr>
			<td>{{ e($sponsor->SCode) }}</td>
		</tr>
		@endforeach
	</table>

	<form action="{{ URL::to('sponsor/add') }}" method="post" class="form">
		<button type="submit" class="btn btn-primary">Generate Code</button>
	</form>
	</div>
</div>
@stop
