@extends('layouts.main')
@section('content')
<!-- Organization navbar -->
<div class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-collapse collapse">
			<div class="navbar-form">
			@foreach ($game->orgs()->get() as $org)
				<a href="{{ URL::to(Request::path().'/'.$org->GOAbbr) }}" class="btn btn-default">{{ $org->GOName }}</a>
			@endforeach
			</div>
		</div><!--/.nav-collapse -->
	</div>
</div>

<!-- default, info, warning, danger, success -->
<a class="label label-info" href="{{ URL::to('games') }}">Back to Games</a>

<?php /*
@if ($auth->gameorgroles()->where('GID', $game->GID)->count() != 0)
<p style="margin-top: 20px"><a class="btn btn-danger" href="{{ URL::to('auth/'.Request::path()) }}">Authorize Members</a></p>
@endif
*/ ?>

<h1>{{ e($game->GName) }} Character List</h1>
<div class="row">
	<div class="col-md-12">

	@if ($auth->gameusers()->where('GID', $game->GID)->count() == 0)

	<h4>You are currently not a part of this game.</p>
	<p style="margin-top: 30px"><a href="{{ URL::to(Request::path().'/link') }}" class="btn btn-success">Link Character</a></p>

	@else

	<h4>Your linked characters:</h4>

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
	<p><a href="{{ URL::to(Request::path().'/link') }}" class="btn btn-success">Add Character</a></p>

	<h4 style="margin-top: 40px">You belong to the following organizations:</h4>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th style="width: 150px;">Organization</th>
			<th>Character Name</th>
		</thead>
		@foreach ($auth->gameusers()->where('GID', $game->GID)->get() as $character)
		@foreach ($character->gameorgs as $org)
		<tr>
			<?php $status = UserStatus::find($org->pivot->USID) ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ e($status->USStatus) }}</span></td>
			@else
				<td><span class="label label-default">{{ e($status->USStatus) }}</span></td>
			@endif
			<td>{{ e($org->GOName) }}</td>
			<td>{{ e($character->GUCachedName) }}</td>
		</tr>
		@endforeach
		@endforeach
	</table>

	@endif

	</div>
</div>
@stop
