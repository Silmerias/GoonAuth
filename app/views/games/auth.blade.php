@extends('layouts.main')
@section('content')

<?php $pending = UserStatus::pending()->first() ?>

<!-- default, info, warning, danger, success -->
<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ e($game->GName) }}</a>

<h1>Users Pending</h1>
<div class="row">
	<div class="col-md-12">

	<?php $query = $game->gameusers()->where('USID', $pending->USID) ?>

	@if ($query->count() == 0)

	<p>There are no users pending verification.</p>
	
	@else

	<p>The following characters are <span class="label label-default">{{ e($pending->USStatus) }}</span>:</p>

	<table class="table">
		<thead>
			<th style="width: 150px;">Goon ID</th>
			<th>Character Name</th>
			<th style="width: 175px;">Reg Date</th>
			<th style="width: 125px;">Post Count</th>
			<th style="width: 100px;">Actions</th>
		</thead>
		@foreach ($query->get() as $gameuser)
		<tr id="GUID_{{ $gameuser->GUID }}">
			<td><a href="{{ URL::to('user/'.$gameuser->user()->first()->UID) }}">{{ e($gameuser->user->UGoonID) }}</a></td>
			<td><a href="{{ GameController::buildGameProfile($game, $gameuser) }}">{{ e($gameuser->GUCachedName) }}</a></td>
			<td>{{ $gameuser->GURegDate }}</td>
			<td>{{ $gameuser->GUCachedPostCount }}</td>
			<td>
				<button type="button" style="margin-right: 5px" class="btn btn-success" onclick="approve({{ $gameuser->GUID }})">Y</a>
				<button type="button" class="btn btn-danger" onclick="deny({{ $gameuser->GUID }})">N</a>
			</td>
		</tr>
		@endforeach
	</table>

	@endif

	</div>
</div>

<script>

function approve(id)
{
	$.ajax({
		url: "{{ URL::to(Request::path()) }}"+'/'+id,
		type: "post",
		dataType: "json",
		data: { action: 'approve' }
	}).done(function(ret) {
		if (ret.success == true)
		{
			$('#GUID_'+id)
				.closest('tr')
				.children('td')
				.wrapInner('<div class="td-slider" />')
				.children(".td-slider")
				.slideUp();
		}
	});
}

function deny(id)
{
	$.ajax({
		url: "{{ URL::to(Request::path()) }}"+'/'+id,
		type: "post",
		dataType: "json",
		data: { action: 'deny' }
	}).done(function(ret) {
		if (ret.success == true)
		{
			$('#GUID_'+id)
				.closest('tr')
				.children('td')
				.wrapInner('<div class="td-slider" />')
				.children(".td-slider")
				.slideUp();
		}
	});
}

</script>

@stop
