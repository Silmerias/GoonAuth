@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<h1>Sponsorship</h1>
<div class="row col-md-12">
	<p>So you wish to sponsor your friend?  Great!  Just remember that you are responsible for them, so if they fuck up, we may also hold you responsible.  Please make sure your friends are coolbros.</p>
</div>
<div class="row col-md-12">
	@if (!$auth->canSponsor())
	<p><a href="#" class="btn btn-success" disabled>Max Sponsors Reached</a></p>
	@else
	<p><a href="{{ URL::to('sponsor/add') }}" class="btn btn-success">Sponsor a Friend</a></p>
	@endif
</div>
<div class="row col-md-12" style="padding-top: 0.5em"><h3>Your Sponsored Friends</h3></div>
<div class="row">
	<div class="col-md-12">
	<p>Your sponsored members are below. You currently have <strong>{{ $auth->sponsoring->count() }}</strong> sponsors registered.</p>
	<table class="table">
		<thead>
			<th style="width: 75px;">Status</th>
			<th>Member Name</th>
		</thead>
		@foreach ($auth->sponsoring()->get() as $sponsor)
		<tr>
			<?php $status = $sponsor->userstatus()->first() ?>
			@if (strcmp($status->USCode, 'ACTI') == 0)
				<td><span class="label label-primary">{{ $status->USStatus }}</span></td>
			@else
				<td><span class="label label-default">{{ $status->USStatus }}</span></td>
			@endif

			<td>{{ e($sponsor->UGoonID) }}</td>
		</tr>
		@endforeach
	</table>
	</div>
</div>
@stop
