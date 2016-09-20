@extends('layouts.main')
@section('content')

<?php
use App\Game;
?>

<div class="row center-block text-center">
	<h2>Wow, good job!</h2>
</div>

<div class="row center-block text-center">
	<div class="register">
		<h3 class="register-heading">Your character has been linked.</h3>
		<p>You can now put in a membership application with any applicable groups for this game.</p>
		<a class="btn btn-lg btn-primary btn-block" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ $game->GName }}</a>
	</div>
</div>
@stop
