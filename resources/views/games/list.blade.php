@extends('layouts.main')
@section('content')
<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<div class="row">
	<div class="col-md-12">

	<div class="register">
	<h1>Games List</h1>
	<a href="{{ URL::to('games/sc') }}" class="register-choice">Star Citizen</a>
	<a href="{{ URL::to('games/mwo') }}" class="register-choice">MechWarrior Online</a>
	</div>
</div>
@stop
