@extends('layouts.main')
@section('content')

<?php
use App\Game;
?>

<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<div class="row">
	<div class="col-md-12">

	<div class="register">
	<h1>Games List</h1>

	@foreach(Game::get() as $game)
		<a href="{{ URL::to('games/' . $game->GAbbr) }}" class="register-choice">{{ $game->GName }}</a>
	@endforeach
	</div>
</div>
@stop
