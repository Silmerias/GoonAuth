@extends('layouts.main')
@section('content')
<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="authed">
			<i class="fa fa-check-circle"></i>
			@if (true)
			<h2>You are authed!</h2>
			@if ($auth->USAUserID)
				<p>Your account is linked to the SA account <strong>{{ $auth->USACachedName }}</strong>.</p>
			@endif
			<p>You are a member of the <strong>{{ $auth->group->GRName }}</strong> group.</p>
			<p>You have <strong>{{ $auth->gameusers->count() }}</strong> registered game users.</p>
			<p>You own <strong>{{ $auth->ownedgroups->count() }}</strong> groups and <strong>{{ $auth->ownedgames->count() }}</strong> games.</p>
			<a href="{{ URL::to('games') }}" class="btn btn-lg btn-success">Join/Manage Game</a> <a href="{{ URL::to('sponsors') }}" class="btn btn-lg btn-primary">Sponsor a Friend</a>
			@else
			<h2>You are sponsored!</h2>
			<p>You were sponsored by <strong>{{ $auth->sponsor()->first()->USACachedName }}</strong>.</p>
			<p>You are a member of the <strong>{{ $auth->group->GRName }}</strong> group.</p>
			<p>You have <strong>{{ $auth->gameusers->count() }}</strong> registered game users.</p>
			<p>You own <strong>{{ $auth->ownedgroups->count() }}</strong> groups and <strong>{{ $auth->ownedgames->count() }}</strong> games.</p>
			<a href="{{ URL::to('games') }}" class="btn btn-lg btn-success">Join/Manage Game</a>
			@endif
		</div>
	</div>
</div>
@stop
