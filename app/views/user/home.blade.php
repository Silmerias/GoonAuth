@extends('layouts.main')
@section('content')

<?php $perms = new UserPerm($auth); ?>
@if ($perms->group()->auth == true)
<p style="margin-top: 20px"><a class="btn btn-danger" href="{{ URL::to('auth/group/'.$auth->group->GRID) }}">Authorize Group Members</a></p>
@endif

<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="authed">
			<i class="fa fa-meh-o"></i>
			<?php
				$sponsors = $auth->sponsors()->where('Sponsor.GRID', $auth->group->GRID)->get();
				$sponsored = !$sponsors->isEmpty();
			?>

			<h2>You are authed!</h2>
			@if ($sponsored == false)
				@if ($auth->USAUserID)
					<p>Your account is linked to the SA account <strong>{{ e($auth->USACachedName) }}</strong>.</p>
				@endif
			@else
				<h2>You were sponsored by {{ e($sponsors->UGoonID) }}.</h2>
			@endif

			<p>You are a member of the <strong>{{ $auth->group->GRName }}</strong> group.</p>
			<p>You have <strong>{{ $auth->gameusers->count() }}</strong> registered game users in <strong>{{ $auth->games->count() }}</strong> games.</p>
			@if ($auth->ownedgroups->count() != 0 || $auth->ownedgameorgs->count() != 0)
				<p>You own <strong>{{ $auth->ownedgroups->count() }}</strong> groups and <strong>{{ $auth->ownedgameorgs->count() }}</strong> game organizations.</p>
			@endif

			<a href="{{ URL::to('games') }}" class="btn btn-lg btn-success">Join/Manage Game</a>

			@if ($sponsored == false)
				<a href="{{ URL::to('sponsor') }}" class="btn btn-lg btn-primary">Sponsor a Friend</a>
			@endif
		</div>
	</div>
</div>
@stop
