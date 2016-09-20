@extends('layouts.main')
@section('content')

<?php
use App\Game;
use App\User;
use App\UserStatus;
?>

<?php
$gameorg = $game->orgs()->first();
?>

<!-- default, info, warning, danger, success -->
<p><a class="label label-info" href="{{ URL::to('games') }}">Back to Games</a></p>

<div class="row col-md-12">
	<h1>{{ $game->GName }}</h1>
	<h5><em>{{ $gameorg->GOName }}</em></h5>
</div>
<div class="row col-md-12" style="padding-top: 1em">
	<h3>Character List</h3>

	@if ($auth->gameusers()->where('GID', $game->GID)->count() == 0)

	<h4>You are currently not a part of this game.</p>
	<p style="margin-top: 30px"><a href="{{ URL::to(Request::path().'/simple-link') }}" class="btn btn-success">Join Game</a></p>

	@else

	<table class="table">
		<thead>
			<th>Character Name</th>
		</thead>
		@foreach ($auth->gameusers()->where('GID', $game->GID)->get() as $character)
		<tr>
			<td>{{ e($character->GUCachedName) }}</td>
		</tr>
		@endforeach
	</table>
	<p><a href="{{ URL::to(Request::path().'/simple-link') }}" class="btn btn-success">Add Character</a></p>

	@endif

	</div>
</div>
@stop
