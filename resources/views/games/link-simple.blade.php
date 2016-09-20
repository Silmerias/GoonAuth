@extends('layouts.main')
@section('content')

<?php
use App\Game;
?>

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ e($game->GName) }}</a>

<div class="row" style="margin-top: 20px">
	<div class="center-block text-center">
		<h2>Please follow the instructions below to join this game.</h2>
	</div>
</div>

<div class="form-register" style="padding-top: 1em">
	<ul>
	@if ($game->GRequireValidation == true)
		<li>Go <a href="{{ $game->GEditProfileURL }}" target="_blank">edit your game profile</a> and place the token below in any of the public fields.</li>
	@endif
		<li>Enter the name you go by in this game and click the Join button.</li>
	</ul>

	<form role="form" action="{{ URL::to(Request::path()) }}" method="post">
		{{ csrf_field() }}

		<div class="col-md-12">

			@if ($game->GRequireValidation == true)
			<p class="form-register-token"><strong>Verification Token</strong>: <kbd>{{ $token }}</kbd></p>
			@else
			<p>&nbsp;</p>
			@endif

			@if (Session::has('error'))
			<div class="alert alert-danger">
				{{ Session::get('error') }}
			</div>
			@endif

			<div id="valid-user" class="alert alert-danger" style="display:none"></div>

			<input type="text" name="username" class="form-control" placeholder="Game Username" required="" autofocus="" onblur="validateUser()">
			<button class="btn btn-lg btn-primary btn-block" type="submit">Join Game</button>
		</div>
	</form>
</div>

<script>
function validateUser()
{
	var user = $('input[name="username"]').val();
	if (user.length == 0)
		return;

	$.ajax({
		url: "{{ URL::to(Request::path().'/check-user') }}",
		type: "post",
		dataType: "json",
		data: { username: user }
	}).done(function(msg) {
		if (msg.valid == "false")
		{
			$('#valid-user').removeClass('alert-success');
			$('#valid-user').addClass('alert-danger')
				.text(msg.message)
				.slideDown();
		}
		else
		{
			$('#valid-user').removeClass('alert-danger');
			$('#valid-user').addClass('alert-success')
				.text("User has not been registered yet.")
				.slideDown();
		}
	}).error(function() {
		$('#valid-user').removeClass('alert-danger');
		$('#valid-user').addClass('alert-success')
			.text("An internal server error has occurred.")
			.slideDown();
	});
}
</script>
@stop
