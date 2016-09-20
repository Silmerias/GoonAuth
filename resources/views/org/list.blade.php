@extends('layouts.main')
@section('content')

<?php
use App\Game;
use App\GameOrg;
?>

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ e($game->GName) }}</a>

<div class="row col-md-12">
	<h1>Organizations List</h1>
</div>

<div class="row">
	@foreach ($game->orgs()->get() as $org)
	<div class="col-md-4">
		<a href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}" class="register-choice">{{ $org->GOName }}</a>
	</div>
	@endforeach
</div>
@stop
