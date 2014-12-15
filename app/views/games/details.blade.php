@extends('layouts.main')
@section('content')
<!-- default, info, warning, danger, success -->
<a class="label label-info" href="{{ URL::to('games') }}">Back to Games</a>

@if ($auth->gameroles()->where('GID', $game->GID)->count() != 0)
<p style="margin-top: 20px"><a class="btn btn-danger" href="{{ URL::to(Request::path().'/auth') }}">Authorize Members</a></p>
@endif

<h1>Character List</h1>
<div class="row">
	<div class="col-md-12">

	@if ($auth->gameusers()->where('GID', $game->GID)->count() == 0)
	
	<p>You are currently not a part of this game.</p>
	<p><a href="{{ URL::to(Request::path().'/join') }}" class="btn btn-success">Join Game</a></p>
	
	@else

	<p>Your registered characters are below.</p>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Character Name</th>
		</thead>
		@foreach ($auth->gameusers()->where('GID', $game->GID)->get() as $character)
		<tr>
			<?php $status = $character->userstatus()->first() ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ e($status->USStatus) }}</span></td>
			@else
				<td><span class="label label-default">{{ e($status->USStatus) }}</span></td>
			@endif
			<td>{{ e($character->GUCachedName) }}</td>
		</tr>
		@endforeach
	</table>

	@endif

	</div>
</div>
@stop
