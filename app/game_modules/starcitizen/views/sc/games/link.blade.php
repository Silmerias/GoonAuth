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

		<p>Go <a href="{{ $game->GEditProfileURL }}" target="_blank">edit your game profile</a> and place the token below in any of the public fields. Once you've done that, enter your username and click the Link button.</p>
		<p class="form-register-token"><strong>Verification Token</strong>: <kbd>{{ $token }}</kbd></p>

		@if (Session::has('error'))
		<div class="alert alert-danger">
			{{ Session::get('error') }}
		</div>
		@endif

		<div id="valid-user" class="alert alert-danger" style="display:none"></div>

		<input type="text" name="username" class="form-control" placeholder="RSI Username" required="" autofocus="" onblur="validateUser()">
		<button class="btn btn-lg btn-primary btn-block" type="submit">Link</button>
	</div>
</form>

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
