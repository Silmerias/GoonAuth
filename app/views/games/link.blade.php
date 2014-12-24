@extends('layouts.main')
@section('content')
<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ e($game->GName) }}</a>

<div class="row" style="margin-top: 20px">
	<div class="col-md-12">
		<p>Please follow the instructions below to add your game account.</p>
	</div>
</div>

<form class="form-register" role="form" action="{{ URL::to(Request::path()) }}" method="post">
@if ($game->GRequireValidation == true)
	<h2 class="form-register-heading">Add your Verification Token</h2>
@else
	<h2 class="form-register-heading">Register your Character Name</h2>
@endif

	<div class="col-md-12">
@if ($game->GRequireValidation == true)
		<p>Go <a href="{{ $game->GEditProfileURL }}" target="_blank">edit your game profile</a> and place the token below in any of the public fields. Once you've done that, enter your username and click the Link button.</p>
		<p class="form-register-token"><strong>Verification Token</strong>: <kbd>{{ $token }}</kbd></p>
@endif
		@if (Session::has('error'))
		<div class="alert alert-danger">
			{{ Session::get('error') }}
		</div>
		@endif
		<input type="text" name="username" class="form-control" placeholder="Game Username" required="" autofocus="">
		<button class="btn btn-lg btn-primary btn-block" type="submit">Link</button>
	</div>
</form>
@stop
