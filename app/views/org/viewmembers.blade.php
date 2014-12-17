@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">Back to {{ e($org->GOName) }}</a>

<h1>Organization Member List</h1>
<div class="row">
	<div class="col-md-12">

	<?php $query = $org->gameusers()->with('User') ?>
	<?php $statuses = UserStatus::get() ?>

	@if ($query->count() == 0)

	<p>There are no members in this organization.</p>

	@else

	<table class="table">
		<thead>
			<th style="width: 100px;">Status</th>
			<th style="width: 150px;">Goon ID</th>
			<th>Character Name</th>
			<th style="width: 100px;">Actions</th>
		</thead>
		@foreach ($query->get() as $gameuser)
		<tr id="ID_{{ $gameuser->GUID }}">
			<td>
				{{ e($statuses[$gameuser->pivot->USID - 1]->USStatus) }}
			</td>
			<td><a href="{{ URL::to('user/'.$gameuser->user->UID) }}">{{ e($gameuser->user->UGoonID) }}</a></td>
			<td><a href="{{ GameController::buildGameProfile($game, $gameuser) }}">{{ e($gameuser->GUCachedName) }}</a></td>
			<td>
				<button type="button" class="btn btn-danger" onclick="kick({{ $gameuser->GUID }})">Kick</a>
			</td>
		</tr>
		@endforeach
	</table>

	@endif

	</div>
</div>

<script>

function kick(id)
{
	/*
	$.ajax({
		url: "{{ URL::to(Request::path()) }}",
		type: "post",
		dataType: "json",
		data: { action: 'deny', id: id }
	}).done(function(ret) {
		if (ret.success == true)
		{
			$('#ID_'+id)
				.closest('tr')
				.children('td')
				.wrapInner('<div class="td-slider" />')
				.children(".td-slider")
				.slideUp();
		}
	});
	*/
}

</script>

@stop
