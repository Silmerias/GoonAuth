@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">Back to {{ e($org->GOName) }}</a>

<h1>Organization Member List</h1>
<div class="row">
	<div class="col-md-12">

	<?php $statuses = UserStatus::get(); ?>

	@if ($members->count() == 0)

	<p>There are no members in this organization.</p>

	@else

	<table class="table">
		<thead>
			<th style="width: 100px;">Status</th>
			<th style="width: 150px;">Goon ID</th>
			<th>Character Name</th>
			<th style="width: 40px;"></th>
			<th style="width: 150px;">Actions</th>
		</thead>
		@foreach ($members as $gameuser)
		<tr id="ID_{{ $gameuser->GUID }}">
			<td>
				{{ e($statuses[$gameuser->pivot->USID - 1]->USStatus) }}
			</td>
			<td><a href="{{ URL::to('user/'.$gameuser->user->UID) }}" target="_blank">{{ e($gameuser->user->UGoonID) }}</a></td>
			<td>{{ GameController::buildGameProfile($game, $gameuser) }}</td>
			<td>
				<button type="button" class="btn btn-note" data-toggle="popover" data-uid="{{ $gameuser->user->UID }}">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true" style="color: goldenrod"></span>
				</button>
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-Kick" data-gid="{{ $gameuser->GUID }}">Kick</button>
				<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal-Note" data-goonid="{{ e($gameuser->user->UGoonID) }}" data-uid="{{ $gameuser->user->UID }}">Add Note</button>
			</td>
		</tr>
		@endforeach
	</table>

	{{ $members->links(); }}

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
						<option value="{{ $nt->NTID }}" color="{{ $nt->NTColor }}">{{ $nt->NTName }}</option>
					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="note-subject" class="control-label">Subject:</label>
					<input type="text" id="note-subject" class="form-control" placeholder="Note subject (optional)"></textarea>
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

			<div class="form-group">
				<div id="note-preview" class="note" style="background-color: white">
					<p class="note-header">
						<span class="note-type">General</span>
						<span class="note-user">- System</span>
						<span class="note-global">[Global]</span>
					</p>
					<p class="note-subject"></p>
					<p class="note-comment"></p>
					<p class="note-footer">By 
						<a href="{{ URL::to('user/'.$auth->UID) }}" target="_blank">{{ e($auth->UGoonID) }}</a>
						- {{ Carbon::now()->toDateTimeString() }}
					</p>
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
	var goonid = button.data('goonid');

	var modal = $(this);
	modal.find('.modal-body textarea').val('');
	modal.find('#note-add').attr('data-id', uid);
	modal.find('#note-global').prop('checked', true);
	modal.find('.note-user').text('- ' + goonid);

	modal.find('#note-type').change();
	modal.find('#note-subject').val('').keyup();
	modal.find('#note-text').keyup();
	modal.find('#note-global').change();
});

$('#note-type').change(function() {
	var o = $('option', this).filter(':selected');
	$('#note-preview').css('backgroundColor', o.attr('color'));
	$('#note-preview .note-type').text(o.text());
});

$('#note-subject').keyup(function() {
	$('#note-preview .note-subject').text($(this).val());
});

$('#note-text').keyup(function() {
	var t = $(this).val();
	t = t.replace('\n', '<br>');
	$('#note-preview .note-comment').html(t);
});

$('#note-global').change(function() {
	if ($(this).prop('checked'))
		$('#note-preview .note-global').show();
	else $('#note-preview .note-global').hide();
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
			subject: $('#note-subject').val(),
			message: $('#note-text').val(),
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

$('[data-toggle="popover"]').click(function() {
	var d = $(this);
	var uid = d.data('uid');

	d.off('click');
	d.off('mouseenter mouseleave');

	$.ajax({
		url: '/user/'+uid+'/notes',
		type: 'get'
	})
	.done(function(ret) {
		d.popover({
			trigger: 'focus',
			html: 'true',
			placement: 'left',
			template: '<div class="popover note-popover"><div class="popover-content"></div></div>',
			content: ret
		}).popover('show');
	});
});

</script>

@stop
