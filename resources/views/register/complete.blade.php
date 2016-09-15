@extends('layouts.main')
@section('content')

<a class="label label-info" href="{{ URL::to('/') }}">Back to Login</a>
<div class="row center-block text-center">
	<p>Wow, good job!</p>
</div>

<div class="register">
	<h2 class="register-heading">Your application has been submitted</h2>
	<p>An e-mail has been sent to your submitted e-mail address.</p>
	<p>If you do not see the e-mail, please check your spam folder.</p>
	<p>You will <strong>get an e-mail</strong> when your application has been accepted or rejected, so make sure you can receive it!</p>
</div>
@stop
