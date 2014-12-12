@extends('layouts.main')
@section('content')
<div class="row">
  <div class="col-md-12">
    <p>Welcome to the auth landing page! We are so very excited that you believe we care about having you! To get started, login using your forum account below. If you're already registered and you're here then you probably know what you need to do.</p>
  </div>
</div>

<form class="form-signin" role="form" action="{{ URL::to('login') }}" method="post">
  <h2 class="form-signin-heading">Login using your forum account</h2>
  <p>If you don't have an account yet, {{ HTML::link(Config::get('goonauth.forumUrl'), "click here") }} to start your auth process.</p>
  @if (Session::has('banned'))
    <div class="alert alert-danger">You have been banned</div>
  @endif
  @if (Session::has('error'))
    <div class="alert alert-danger">
    {{ Session::get('error') }}
    </div>
  @endif
  @if (false)
  <input type="text" name="username" class="form-control" placeholder="Email address/Username" required="" autofocus="">
  <input type="password" name="password" class="form-control" placeholder="Password" required="">
  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
  @endif
</form>
@stop