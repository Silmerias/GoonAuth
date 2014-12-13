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
			<td>{{ $user->UGoonID }}</td>
		</tr>		<tr>
			<td><span class="label label-default">Group</span></td>
			<td>{{ $user->group()->first()->GRName }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">E-Mail</span></td>
			<td>{{ $user->UEmail }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">Sponsored?</span></td>
			<td>{{ (empty($user->USponsorID) ? 'No' : 'YES') }}</td>
		</tr>
		<tr>
			<td><span class="label label-default">SA Name</span></td>
			<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ $user->USACachedName }}</a></td>
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
				<td>{{ inet_ntop($user->UIPAddress) }}</td>
			@endif
		</tr>
	</table>

	</div>
</div>
@stop
