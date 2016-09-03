@extends('layouts.main')
@section('content')

<?php
use App\Game;
use App\GameOrg;
?>

<a class="label label-info" href="{{ URL::to('games/'.$game->GAbbr) }}">Back to {{ e($game->GName) }}</a>

<div class="row">
	<div class="col-md-12">

	<div class="register">
	<h1>Organizations List</h1>
	@foreach ($game->orgs()->get() as $org)
		<a href="{{ URL::to('games/'.$game->GAbbr.'/'.$org->GOAbbr) }}" class="register-choice">{{ $org->GOName }}</a>
	@endforeach
	</div>
</div>
@stop
