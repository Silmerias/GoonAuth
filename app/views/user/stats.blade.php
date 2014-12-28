@extends('layouts.main')
@section('content')
<!-- default, info, warning, danger, success -->
<a class="label label-info" href="{{ URL::previous() }}">Back</a>

<h1>User Details</h1>
<div class="row">
	<div class="col-md-12">

	<table class="table">
		<thead>
			<th style="width: 75px;">Stat</th>
			<th>Value</th>
		</thead>
		<tr>
			<td><span class="label label-default">Goon ID</span></td>
			<td>{{ e($user->UGoonID) }}</td>
		</tr>		<tr>
			<td><span class="label label-default">Group</span></td>
			<td>{{ e($user->group->GRName) }}</td>
		</tr>
@if (false)
		<tr>
			<td><span class="label label-default">E-Mail</span></td>
			<td>{{ e($user->UEmail) }}</td>
		</tr>
@endif
		<tr>
			<td><span class="label label-default">SA Name</span></td>
			@if (is_null($user->USAUserID))
				<td></td>
			@else
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ e($user->USACachedName) }}</a></td>
			@endif
		</tr>
		<tr>
			<td><span class="label label-default">SA Reg Date</span></td>
			<td>{{ $user->USARegDate }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">SA Post Count</span></td>
			<td>{{ $user->USACachedPostCount }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">IP Address</span></td>
			@if (is_null($user->UIPAddress))
				<td></td>
			@else
				<?php
					$ip = $user->UIPAddress;
					if (is_null($ip)) $ip = '';
					else $ip = inet_ntop(pack('H*', $ip));
				?>
				<td>{{ $ip }}</td>
			@endif
		</tr>
	</table>

	<?php
		$sponsors = $user->sponsors()->get();
		$sponsoring = $user->sponsoring()->get();
	?>

	@if (!$sponsors->isEmpty())

	<p>Sponsored by:</p>

	<table class="table">
		<thead>
			<th>Sponsor</th>
			<th>Group</th>
			<th>Game Organization</th>
		</thead>
		<tbody>
		@foreach ($sponsors as $s)
			<tr>
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($s->USACachedName) }}">{{ e($s->USACachedName) }}</a></td>
				@if (is_null($s->pivot->GRID))
					<td></td>
				@else
					<td>{{ Group::find($s->pivot->GRID)->GRName }}</td>
				@endif

				@if (is_null($s->pivot->GOID))
					<td></td>
				@else
					<td>{{ GameOrg::find($s->pivot->GOID)->GOName }}</td>
				@endif
			</tr>
		@endforeach
		</tbody>
	</table>
	@endif

	@if (!$sponsoring->isEmpty())

	<p>Sponsored members:</p>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Goon ID</th>
		</thead>
		<tbody>
		@foreach ($sponsoring as $s)
			<tr>
				<?php $status = $s->userstatus()->first() ?>
				@if (strcmp($status->USCode, 'ACTI') == 0)
					<td><span class="label label-primary">{{ $status->USStatus }}</span></td>
				@else
					<td><span class="label label-default">{{ $status->USStatus }}</span></td>
				@endif
				<td><a href="{{ URL::to('user/'.$s->UID) }}">{{ $s->UGoonID }}</a></td>
			</tr>
		@endforeach
		</tbody>
	</table>

	@endif

	@if (isset($notes) && !empty($notes))

	<h4>Notes:</h4>

	<div id="note-list">
	@foreach ($notes as $note)
		<div class="note" style="background-color: {{ $note->NTColor }}">
			<p class="note-header">
				<span class="note-type">{{ e($note->NTName) }}</span>
				<span class="note-user">- {{ e($note->UGoonID) }}</span>
				@if ($note->NGlobal == true)
					<span class="note-global">[Global]</span>
				@endif
			</p>
			<p class="note-comment">{{ str_replace("\n", '<br>', e($note->NNote)) }}</p>
			<p class="note-footer">By
				@if (is_null($note->CreatedGoonID))
					System
				@else
					<a href="{{ URL::to('user/'.$note->CreatedUID) }}">{{ e($note->CreatedGoonID) }}</a>
				@endif
				- {{ with(new Carbon($note->NTimestamp))->toDateTimeString() }}
			</p>
		</div>
	@endforeach
	</div>

	@endif

	</div>
</div>

@stop
