@extends('layouts.main')
@section('content')
<div class="row">
	<div class="col-md-12">
		<p>Please follow the instructions below.</p>
	</div>
</div>

<form class="form-register" role="form" action="{{ URL::action('RegisterController@postGoon') }}" method="post">
	<h2 class="form-register-heading">Are you being sponsored?</h2>
	<!-- <p>{{ HTML::link(URL::to('register/goon-sponsored'), "Click here") }} to access the sponsor form.</p> -->
	<p>The sponsor form is not yet completed. Very sorry about that.</p>

	<h2 class="form-register-heading">Fill out the following</h2>
	<p>Your Goon ID is your login name. Your Display Name can be changed later, but remember that your Goon ID is always visible.</p>
	<p>Make sure your e-mail is valid as you will receive your acceptance information through it.</p>

	@if (Session::has('error'))
		<div class="alert alert-danger">
			{{ Session::get('error') }}
		</div>
	@else
		<div class="alert alert-danger" style="display:none"></div>
	@endif

	<div id="valid-goonid" class="alert alert-danger" style="display:none"></div>
	<div id="valid-email" class="alert alert-danger" style="display:none"></div>

	<input type="text" name="goonid" class="form-control" placeholder="Desired Goon ID" required="" autofocus="" onblur="validateGoonID()">
	<input type="email" name="email" class="form-control" placeholder="E-Mail Address" required="" autofocus="" onblur="validateEmail()">
	<button class="btn btn-lg btn-primary btn-block" type="submit">Next</button>
</form>

<script>
function validateGoonID()
{
	var goonid = $('input[name="goonid"]').val();
	if (goonid.length == 0)
		return;

	$.ajax({
		url: "{{ URL::to('register/check-goon') }}",
		type: "post",
		dataType: "json",
		data: { goonid: goonid }
	}).done(function(msg) {
		if (msg.valid == "false")
		{
			$('#valid-goonid').removeClass('alert-success');
			$('#valid-goonid').addClass('alert-danger')
				.text(msg.message)
				.slideDown();
		}
		else
		{
			$('#valid-goonid').removeClass('alert-danger');
			$('#valid-goonid').addClass('alert-success')
				.text("Goon ID is available.")
				.slideDown();
		}
	}).error(function() {
		$('#valid-goonid').removeClass('alert-danger');
		$('#valid-goonid').addClass('alert-success')
			.text("An internal server error has occurred.")
			.slideDown();
	});
}

function validateEmail()
{
	var email = $('input[name="email"]').val();
	if (email.length == 0)
		return;

	$.ajax({
		url: "{{ URL::to('register/check-email') }}",
		type: "post",
		dataType: "json",
		data: { email: email }
	}).done(function(msg) {
		if (msg.valid == "false")
		{
			$('#valid-email').removeClass('alert-success');
			$('#valid-email').addClass('alert-danger')
				.text(msg.message)
				.slideDown();
		}
		else
		{
			$('#valid-email').removeClass('alert-danger');
			$('#valid-email').addClass('alert-success')
				.text("E-mail is acceptable.")
				.slideDown();
		}
	}).error(function() {
		$('#valid-goonid').removeClass('alert-danger');
		$('#valid-goonid').addClass('alert-success')
			.text("An internal server error has occurred.")
			.slideDown();
	});
}
</script>
@stop
