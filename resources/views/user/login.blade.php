@extends('layouts.main')
@section('content')
<div class="row">
	<div class="col-md-12">
		<p>Welcome to the auth landing page! We are so very excited that you believe we care about having you!</p>
	</div>
</div>

<form class="form-signin" role="form" action="{{ URL::to('login') }}" method="post">
	{{ csrf_field() }}
	<h2 class="form-signin-heading">Login using your Goon ID</h2>
	<p>If you don't have an account yet, {{ HTML::link(URL::to('register'), "click here") }} to start your auth process.</p>
	@if (Session::has('banned'))
		<div class="alert alert-danger">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>
			You have been banned
		</div>
	@endif
	@if (Session::has('error'))
		<div class="alert alert-danger">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only">Error:</span>
			{{ Session::get('error') }}
		</div>
	@endif

	<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
	<input type="text" name="goonid" class="form-control" placeholder="Goon ID" required="" autofocus="">
	<input type="password" name="password" class="form-control" placeholder="Password" required="">
	<label class="persist"><input type="checkbox" name="persist">Remember me?</label>
	<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
</form>
@stop
