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
			<th style="width: 100px;">Actions</th>
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
				<button type="button" style="margin-right: 5px" class="btn btn-success" onclick="approve({{ $user->UID }})">Y</a>
				<button type="button" class="btn btn-danger" onclick="deny({{ $user->UID }})">N</a>
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
