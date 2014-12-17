@extends('layouts.main')
@section('content')
<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">Back to {{ e($org->GOName) }}</a>

<div class="row" style="margin-top: 20px">
	<div class="col-md-12">
		<p>Please follow the instructions below to request organization membership.</p>
	</div>
</div>

<?php
$users = array();
foreach ($auth->gameusers()->where('GID', $game->GID)->get() as $character)
{
	if ($org->gameusers()->where('GameUser.GUID', $character->GUID)->count() == 0)
		$users = array_add($users, $character->GUID, $character->GUCachedName);
}
?>

<form class="form-register" role="form" action="{{ URL::to(Request::path()) }}" method="post">
	<h2 class="form-register-heading">Add Character</h2>
	<div class="col-md-12">
		<p>Select your character then click the Join button to request membership.</p>
		@if (empty($users))
			<p class="label label-danger">No available characters.</p>
		@else
			<select name="character" class="form-control" required="">
			@foreach ($users as $i => $v)
				<option value="{{ $i }}">{{ e($v) }}</option>
			@endforeach
			</select>
			<textarea name="comment" class="form-control" placeholder="Comment (optional)"></textarea>
			<button class="btn btn-lg btn-primary btn-block" type="submit">Join</button>
		@endif
	</div>
</form>
@stop
