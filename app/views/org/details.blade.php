@extends('layouts.main')
@section('content')
<!-- Organization navbar -->
<div class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-collapse collapse">
			@foreach ($game->orgs()->get() as $org)
				<ul class="nav navbar-nav">
					<li><a href="{{ URL::to(Request::path().'/'.$org->GOAbbr) }}">{{ $org->GOName }}</a></li>
				</ul>
			@endforeach
		</div>
	</div>
</div>

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ $game->GName }}</a>

@if ($auth->gameorgroles()->where('GOID', $org->GOID)->count() != 0)
<p style="margin-top: 20px"><a class="btn btn-danger" href="{{ URL::to('auth/'.Request::path()) }}">Authorize Members</a></p>
@endif

<h1>{{ e($org->GOName) }} Character List</h1>
<div class="row">
	<div class="col-md-12">

	@if ($org->gameusers()->where('UID', $auth->UID)->count() == 0)

	<h4>You are currently not a part of this organization.</p>
	<p style="margin-top: 30px"><a href="{{ URL::to(Request::path().'/join') }}" class="btn btn-success">Join Organization</a></p>

	@else

	<h4>Your linked characters:</h4>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Character Name</th>
		</thead>
		@foreach ($org->gameusers()->where('UID', $auth->UID)->get() as $character)
		<tr>
			<?php $status = UserStatus::find(GameOrgHasGameUser::where('GOID', $org->GOID)->where('GUID', $character->GUID)->first()->USID) ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ e($status->USStatus) }}</span></td>
			@else
				<td><span class="label label-default">{{ e($status->USStatus) }}</span></td>
			@endif
			<td>{{ e($character->GUCachedName) }}</td>
		</tr>
		@endforeach
	</table>

	<p><a href="{{ URL::to(Request::path().'/join') }}" class="btn btn-success">Add Character</a></p>

	@endif

	</div>
</div>
@stop
