@extends('layouts.main')
@section('content')
<a class="label label-info" href="{{ URL::to('games') }}">Back to Games</a>

<div class="row" style="margin-top: 20px">
	<div class="col-md-12">
		<p>Please follow the instructions below to add your game account.</p>
	</div>
</div>

<form class="form-register" role="form" action="{{ URL::to(Request::path().'/link') }}" method="post">
	<h2 class="form-register-heading">Add your Verification Token</h2>
	<div class="col-md-12">
		<p>Go <a href="{{ $game->GProfileURL }}" target="_blank">edit your game profile</a> and place the token below in any of the public fields. Once you've done that, enter your username and click the Request button.</p>
		<p class="form-register-token"><strong>Verification Token</strong>: <kbd>{{ $token }}</kbd></p>
		@if (Session::has('error'))
		<div class="alert alert-danger">
			{{ Session::get('error') }}
		</div>
		@endif
		<input type="text" name="username" class="form-control" placeholder="Game Username" required="" autofocus="">
		<button class="btn btn-lg btn-primary btn-block" type="submit">Request</button>
	</div>
</form>
@stop
