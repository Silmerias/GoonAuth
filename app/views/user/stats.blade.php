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
		<tr>
			<td><span class="label label-default">E-Mail</span></td>
			<td>{{ e($user->UEmail) }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">Sponsor</span></td>
			@if (is_null($user->sponsor))
				<td></td>
			@else
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->sponsor->USACachedName) }}">{{ e($user->sponsor->USACachedName) }}</a></td>
			@endif
		</tr>
		<tr>
			<td><span class="label label-default">SA Name</span></td>
			<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ e($user->USACachedName) }}</a></td>
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
			@if (empty($user->UIPAddress))
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

	@if ($user->sponsoring()->count() != 0)

	<p>Sponsored members:</p>

	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Goon ID</th>
		</thead>
		@foreach ($user->sponsoring()->get() as $sponsored)
		<tr>
			<?php $status = $sponsored->userstatus()->first() ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ $status->USStatus }}</span></td>
			@else
				<td><span class="label label-default">{{ $status->USStatus }}</span></td>
			@endif
			<td><a href="{{ URL::to('user/'.$sponsored->UID) }}">{{ $sponsored->UGoonID }}</a></td>
		</tr>
		@endforeach
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
