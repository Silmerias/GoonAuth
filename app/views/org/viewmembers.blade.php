@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">Back to {{ e($org->GOName) }}</a>

<h1>Organization Member List</h1>
<div class="row">
	<div class="col-md-12">

	<?php
		$rejected = UserStatus::where('USCode', 'REJE')->first();
		$query = $org->gameusers()->where('GameOrgHasGameUser.USID', '<>', $rejected->USID)->get();

		/*
		$queries = DB::getQueryLog();
		$last_query = end($queries);
		print_r($last_query);
		*/

		$statuses = UserStatus::get();
	?>

	@if ($query->count() == 0)

	<p>There are no members in this organization.</p>

	@else

	<table class="table">
		<thead>
			<th style="width: 100px;">Status</th>
			<th style="width: 150px;">Goon ID</th>
			<th>Character Name</th>
			<th style="width: 150px;">Actions</th>
		</thead>
		@foreach ($query as $gameuser)
		<tr id="ID_{{ $gameuser->GUID }}">
			<td>
				{{ e($statuses[$gameuser->pivot->USID - 1]->USStatus) }}
			</td>
			<td><a href="{{ URL::to('user/'.$gameuser->user->UID) }}">{{ e($gameuser->user->UGoonID) }}</a></td>
			<td><a href="{{ GameController::buildGameProfile($game, $gameuser) }}">{{ e($gameuser->GUCachedName) }}</a></td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" onclick="kick({{ $gameuser->GUID }})">Kick</a>
				<button type="button" class="btn btn-sm btn-warning" style="margin-left: 5px" onclick="addnote({{ $gameuser->user->UID }})">Add Note</button>
			</td>
		</tr>
		@endforeach
	</table>

	@endif

	</div>
</div>

<div id="dialog-note" title="Create Note">
	Note Type: <select id="notetype" name="notetype">
	@foreach (NoteType::with(array('roles.gameorgusers' => function($q) use($auth) { $q->where('GameOrgAdmin.UID', $auth->UID); }))->where('NTSystemUseOnly', 'false')->get() as $nt)
		<option value="{{ $nt->NTID }}">{{ $nt->NTName }}</option>
	@endforeach
	</select>
	<textarea id="notetext" name="notetext" placeholder="Type your note here" style="margin-top: 10px; width: 310px; height: 140px"></textarea>
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

function addnote(id)
{
	$('#notetext').val('');
	$('#dialog-note').attr('uid', id);
	dialog.dialog("open");
}

function submitnote()
{
	$.ajax({
		url: "{{ URL::to(Request::path()) }}",
		type: "post",
		dataType: "json",
		data: {
			action: 'addnote',
			id: $('#dialog-note').attr('uid'),
			type: $('#notetype').val(),
			text: $('#notetext').val()
		}
	}).done(function(ret) {
		dialog.dialog("close");
	});	
}

dialog = $('#dialog-note').dialog({
	autoOpen: false,
	height: 300,
	width: 350,
	modal: true,
	buttons: {
		'Add Note': function() {
			submitnote();
		},
		Cancel: function() {
			dialog.dialog("close");
		}
	}
});

</script>

@stop
