@extends('layouts.main')
@section('content')

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
			<th style="width: 150px;">Goon ID</th>
			<th>SA Name</th>
			<th>Sponsor</th>
			<th style="width: 175px;">SA Reg Date</th>
			<th style="width: 125px;">SA Post Count</th>
			<th style="width: 125px;">Actions</th>
		</thead>
		@foreach ($query->get() as $user)
		<tr id="ID_{{ $user->UID }}">
			<td><a href="{{ URL::to('user/'.$user->UID) }}">{{ e($user->UGoonID) }}</a></td>
			<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ e($user->USACachedName) }}</a></td>
			@if (is_null($user->sponsor))
				<td></td>
			@else
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->sponsor->USACachedName) }}">{{ e($user->sponsor->USACachedName) }}</a></td>
			@endif
			<td>{{ $user->USARegDate }}</td>
			<td>{{ $user->USACachedPostCount }}</td>
			<td>
				<button type="button" class="btn btn-success" data-auth="true" data-uid="{{ $user->UID }}">Y</button>
				<button type="button" class="btn btn-danger" data-auth="false" data-uid="{{ $user->UID }}">N</button>
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

$('.table button').click(function (event) {
	var btn = $(this);

	var auth = btn.data('auth');
	var uid = btn.data('uid');
	if (auth == undefined || uid == undefined)
		return true;

	if (auth == true)
	{
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

</script>

@stop
