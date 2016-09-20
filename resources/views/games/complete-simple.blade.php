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
		<h3 class="register-heading">Your have joined the game!</h3>
		<p>You are now ready to, like, do stuff.  Okay?</p>
		<a class="btn btn-lg btn-primary btn-block" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ $game->GName }}</a>
	</div>
</div>
@stop
