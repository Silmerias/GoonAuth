@extends('layouts.main')
@section('content')

<?php
use App\Game;
?>

<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<div class="row col-md-12">
	<h1>Games List</h1>
</div>

<div class="row">
	@foreach(Game::get() as $game)
	<div class="col-md-4">
		<a href="{{ URL::to('games/' . $game->GAbbr) }}" class="register-choice">{{ $game->GName }}</a>
	</div>
	@endforeach
</div>
@stop
