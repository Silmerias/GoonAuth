@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('register/type') }}">Back to Registration Type</a>
<div class="row center-block text-center">
	<p>Please follow the instructions below.</p>
</div>

<form class="form-register" role="form" action="{{ URL::action('RegisterController@postGoon') }}" method="post">
	{{ csrf_field() }}
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

	<input type="text" name="goonid" class="form-control" placeholder="Desired Goon ID" required="" autofocus="" onblur="validate(this)" data-type="goonid">
	<input type="email" name="email" class="form-control" placeholder="E-Mail Address" required="" autofocus="" onblur="validate(this)" data-type="email">
	<button class="btn btn-lg btn-primary btn-block" type="submit">Next</button>
</form>

<script>
function validate(element)
{
	var datatype = $(element).data('type');
	var val = $(element).val();
	if (val.length == 0)
		return;

	// Our data packet.
	var data = {};
	data[datatype] = val;

	$.ajax({
		url: "{{ URL::to('register/check') }}",
		type: "post",
		dataType: "json",
		data: data
	}).done(function(msg) {
		var e = $('#valid-'+datatype);
		if (msg.valid == "false")
			e.removeClass('alert-success').addClass('alert-danger');
		else e.removeClass('alert-danger').addClass('alert-success');

		e.html(msg.message).slideDown();
	}).error(function() {
		$('#valid-'+datatype)
			.removeClass('alert-danger')
			.addClass('alert-success')
			.text("An internal server error has occurred.")
			.slideDown();
	});
}
</script>
@stop
