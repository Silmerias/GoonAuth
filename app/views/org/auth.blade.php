@extends('layouts.main')
@section('content')

<?php $pending = UserStatus::pending()->first() ?>

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}">Back to {{ e($org->GOName) }}</a>

<h1>Users Pending</h1>
<div class="row">
	<div class="col-md-12">

	<?php $query = $org->gameusers()->where('GameOrgHasGameUser.USID', $pending->USID) ?>

	@if ($query->count() == 0)

	<p>There are no members pending verification.</p>

	@else

	<p>The following members are <span class="label label-default">{{ e($pending->USStatus) }}</span>:</p>

	<table class="table">
		<thead>
			<th style="width: 150px;">Goon ID</th>
			<th>Character Name</th>
			<th style="width: 175px;">Reg Date</th>
			<th style="width: 125px;">Post Count</th>
			<th style="width: 100px;">Actions</th>
		</thead>
		@foreach ($query->get() as $gameuser)
		<tr id="ID_{{ $gameuser->GUID }}">
			<td><a href="{{ URL::to('user/'.$gameuser->user->UID) }}">{{ e($gameuser->user->UGoonID) }}</a></td>
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

<div id="dialog-reject-reason" title="Rejection Reason">
	<textarea id="reject-text" name="eject-text" placeholder="Reason for rejection" style="width: 310px; height: 170px; padding: 5px"></textarea>
</div>

<script>

function approve(id)
{
	$.ajax({
		url: "{{ URL::to(Request::path()) }}",
		type: "post",
		dataType: "json",
		data: { action: 'approve', id: id }
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
}

function deny(id)
{
	$('#dialog-reject-reason').attr('uid', id);
	$('#dialog-reject-reason').dialog('open');
}

reject = $('#dialog-reject-reason').dialog({
	autoOpen: false,
	height: 300,
	width: 350,
	modal: true,
	buttons: {
		'Reject': function() {
			$.ajax({
				url: "{{ URL::to(Request::path()) }}",
				type: "post",
				dataType: "json",
				data: {
					action: 'deny',
					id: $('#dialog-reject-reason').attr('uid'),
					text: $('#reject-text').val()
				}
			}).done(function(ret) {
				reject.dialog("close");
				if (ret.success == true)
				{
					$('#ID_'+$('#dialog-reject-reason').attr('uid'))
						.closest('tr')
						.children('td')
						.wrapInner('<div class="td-slider" />')
						.children(".td-slider")
						.slideUp();
				}
			});
		},
		Cancel: function() {
			reject.dialog("close");
		}
	}
});

</script>

@stop
