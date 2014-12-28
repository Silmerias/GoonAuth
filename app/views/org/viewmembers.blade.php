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
			<td>{{ GameController::buildGameProfile($game, $gameuser) }}</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-Kick" data-gid="{{ $gameuser->GUID }}">Kick</button>
				<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal-Note" data-uid="{{ $gameuser->user->UID }}">Add Note</button>
			</td>
		</tr>
		@endforeach
	</table>

	@endif

	</div>
</div>

<div class="modal fade" id="modal-Error" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Error</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
					<span class="error"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-Note" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Note</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="note-type" class="control-label">Note Type:</label>
					<select id="note-type" class="form-control">
					@foreach (NoteType::with(array('roles.gameorgusers' => function($q) use($auth, $org) { $q->where('GameOrgAdmin.UID', $auth->UID)->where('GameOrgAdmin.GOID', $org->GOID); }))->where('NTSystemUseOnly', 'false')->get() as $nt)
						<option value="{{ $nt->NTID }}">{{ $nt->NTName }}</option>
					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="note-text" class="control-label">Message:</label>
					<textarea id="note-text" class="form-control" placeholder="Type your note here"></textarea>
				</div>
				<div class="input-group">
					<span class="input-group-addon">
						<input id="note-global" type="checkbox">
					</span>
					<label for="note-global" class="form-control text-sm">Global note?</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="note-add" class="btn btn-primary">Add Note</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>

function error(msg)
{
	$('#modal-Error .modal-body .error').text(msg);
	$('#modal-Error').modal('show');
}

$('#modal-Note').on('show.bs.modal', function (event) {
	var button = $(event.relatedTarget);
	var uid = button.data('uid');

	var modal = $(this);
	modal.find('.modal-body textarea').val('');
	modal.find('#note-add').attr('data-id', uid);
	modal.find('#note-global').prop('checked', false);
});

$('#note-add').click(function (event) {
	var id = $(this).attr('data-id');

	$.ajax({
		url: '{{ URL::to(Request::path()) }}',
		type: 'post',
		dataType: 'json',
		data: {
			action: 'addnote',
			id: id,
			type: $('#note-type').val(),
			text: $('#note-text').val(),
			global: $('#note-global').prop('checked')
		}
	})
	.done(function(ret) {
		$('#modal-Note').modal('hide');
		if (ret.success == false)
		{
			$('#modal-Note').modal('hide');
			error(ret.message);
		}
	})
	.fail(function(ret) {
		$('#modal-Note').modal('hide');
		error('An internal server error has occurred.');
	});
});

</script>

@stop
