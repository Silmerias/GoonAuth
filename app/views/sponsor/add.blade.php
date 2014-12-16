@extends('layouts.main')
@section('content')
<h1>Sponsor a Friend</h1>
<div class="row">
	<div class="col-md-12">
	<p>Sponsoring a friend is easy assuming you/him are not complete idiots! Just follow the steps below to get them authed.</p>
	<ol>
		<li>Have them register an account on the <a href="http://aagoons.com" target="_blank">forums</a>.</li>
		<li>Enter in their forum account name below.</li>
	</ol>
	<p>That's all you have to do! <strong>Remember! You are completely responsible for the actions of your friend. If the guy you sponsor fucks up, both you and him will be kicked from the guild. Make sure you trust who you sponsor!</strong></p>
	@if (Session::has('error'))
	<div class="alert alert-danger">
		{{ Session::get('error') }}
	</div>
	@endif
	<form action="{{ URL::to('sponsor/add') }}" method="post" class="form">
		<div class="form-group">
			<input type="text" name="username" class="form-control" placeholder="Forum Username" required>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Sponsor Account</button>
		</div>
	</form>
	</div>
</div>
@stop
