@extends('layouts.main')
@section('content')
<div class="row">
	<div class="col-md-12">
		<p>Wow, good job!</p>
	</div>
</div>

<div class="register">
	<h2 class="register-heading">Your character has been linked.</h2>
	<p>You can now put in a membership application with any applicable groups for this game.</p>
	<a class="btn btn-lg btn-primary btn-block" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ $game->GName }}</a>
</div>
@stop
