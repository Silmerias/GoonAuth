@extends('layouts.main')
@section('content')

<?php $auth = Auth::user(); ?>
<?php $pending = UserStatus::pending()->first() ?>

<!-- default, info, warning, danger, success -->
<!-- <a class="label label-info" href="{{ URL::to('group/'.$group->GRID) }}">Back to {{ $group->GRName }}</a> -->
<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<h1>Users Pending</h1>
<div class="row">
	<div class="col-md-12">

	<?php $query = $group->members()->where('USID', $pending->USID) ?>

	@if ($query->count() == 0)

	<p>There are no members pending verification.</p>

	@else

	<p>The following members are <span class="label label-default">{{ e($pending->USStatus) }}</span>:</p>

	<table class="table">
		<thead>
			<th>Goon ID</th>
			<th>SA Name</th>
			<th>Sponsor</th>
			<th style="width: 175px;">SA Reg Date</th>
			<th style="width: 125px;">SA Post Count</th>
			<th style="width: 140px;"></th>
			<th style="width: 125px;">Actions</th>
		</thead>
		@foreach ($query->get() as $user)
		<tr id="ID_{{ $user->UID }}">
			<td><a href="{{ URL::to('user/'.$user->UID) }}">{{ e($user->UGoonID) }}</a></td>
			<td class="progress-bar-here">
				<a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ e($user->USACachedName) }}</a>
			</td>
			@if ($user->sponsors()->count() === 0)
				<td></td>
			@else
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->sponsors->first()->USACachedName) }}">{{ e($user->sponsors->first()->USACachedName) }}</a></td>
			@endif
			<td>{{ $user->USARegDate }}</td>
			<td>{{ $user->USACachedPostCount }}</td>
			<td>
				<button type="button" class="btn btn-note" data-toggle="popover" data-uid="{{ $user->UID }}">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true" style="color: goldenrod"></span>
				</button>
				<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal-Note" data-goonid="{{ e($user->UGoonID) }}" data-uid="{{ $user->UID }}" data-url="{{ URL::to(Request::path()) }}">Add Note</button>
			</td>
			<td>
				<button type="button" class="btn btn-auth btn-success" data-auth="true" data-uid="{{ $user->UID }}">Y</button>
				<button type="button" class="btn btn-auth btn-danger" data-auth="false" data-uid="{{ $user->UID }}">N</button>
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

<div class="modal fade" id="modal-Reject" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Reject Player</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="reject-text" class="control-label">Reason:</label>
						<textarea id="reject-text" class="form-control" placeholder="Reason for rejection"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="reject-btn" class="btn btn-default">Reject</button>
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
					@foreach (NoteType::with(array('roles.groupusers' => function($q) use($auth, $group) { $q->where('GroupAdmin.UID', $auth->UID)->where('GroupAdmin.GRID', $group->GRID); }))->where('NTSystemUseOnly', 'false')->get() as $nt)
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
{{ HTML::script('assets/js/goonauth-addnote.js') }}

<script>


function error(msg)
{
	$('#modal-Error .modal-body .error').text(msg);
	$('#modal-Error').modal('show');
}

function close(id)
{
	$('#ID_'+id)
		.closest('tr')
		.children('td')
		.wrapInner('<div class="td-slider" />')
		.children(".td-slider")
		.slideUp();
}

$('#modal-Reject').on('show.bs.modal', function (event) {
	var modal = $(this);
	modal.find('.modal-body textarea').val('');
});

$('.table button.btn-auth').click(function (event) {
	var btn = $(this);

	var auth = btn.data('auth');
	var uid = btn.data('uid');
	if (auth == undefined || uid == undefined)
		return true;

	// Don't process pending records.
	var row = $('#ID_'+uid);
	if (row.attr('status') === 'pending')
		return true;

	if (auth == true)
	{
		// Set to pending.
		row.attr('status', 'pending');

		// Add the progress bar.
		var p = $('.progress-bar-here', row);
		p.html(
			'<div class="progress">'
			+'	<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: 100%; text-align:left">'
			+		p.html()
			+'	</div>'
			+'</div>'
		);

		// Send the action.
		$.ajax({
			url: '{{ URL::to(Request::path()) }}',
			type: 'post',
			dataType: 'json',
			data: { action: 'approve', id: uid }
		})
		.done(function(ret) {
			if (ret.success == true)
				close(uid);
			else error(ret.message);
		})
		.fail(function(ret) {
			error('An internal server error has occurred.');
		});
	}
	else
	{
		$('#reject-btn').attr('data-id', uid);
		$('#modal-Reject').modal('show');
	}

	return false;
});

$('#reject-btn').click(function (event) {
	var id = $('#reject-btn').attr('data-id');

	// Set the row as pending action.
	var row = $('#ID_'+id);
	row.attr('status', 'pending');

	// Add the progress bar.
	var p = $('.progress-bar-here', row);
	p.html(
		'<div class="progress">'
		+'	<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" style="width: 100%; text-align:left">'
		+		p.html()
		+'	</div>'
		+'</div>'
	);

	$.ajax({
		url: '{{ URL::to(Request::path()) }}',
		type: 'post',
		dataType: 'json',
		data: {
			action: 'deny',
			id: id,
			text: $('#reject-text').val()
		}
	})
	.done(function(ret) {
		$('#modal-Reject').modal('hide');
		if (ret.success == true)
			close(id);
		else error(ret.message);
	})
	.fail(function(ret) {
		$('#modal-Reject').modal('hide');
		error('An internal server error has occurred.');
	});

	return false;
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
