@extends('layouts.main')
@section('content')
<div class="row">
	<div class="col-md-12">
		<p>Please follow the instructions below to verify your SA account.</p>
	</div>
</div>

<form class="form-register" role="form" action="{{ URL::action('RegisterController@postLink') }}" method="post">
	<h2 class="form-register-heading">Add your Verification Token</h2>
	<div class="col-md-12">
		<p>Go <a href="http://forums.somethingawful.com/member.php?action=editprofile" target="_blank">edit your profile</a> on SA and place the token below in any of the public fields (e.g., about me, location, interests, etc). Once you've done that, enter your username and click the Verify button.</p>
		<p class="form-register-token"><strong>Verification Token</strong>: <kbd>{{ $token }}</kbd></p>
		@if (Session::has('error'))
		<div class="alert alert-danger">
			{{ Session::get('error') }}
		</div>
		@endif
		<input type="text" name="sa_username" class="form-control" placeholder="Something Awful Username" required="" autofocus="">
		<button class="btn btn-lg btn-primary btn-block" type="submit">Next</button>
	</div>
</form>
@stop
