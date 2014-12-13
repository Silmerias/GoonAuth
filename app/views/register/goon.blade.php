@extends('layouts.main')
@section('content')
<div class="row">
  <div class="col-md-12">
    <p>Please follow the instructions below.</p>
  </div>
</div>

<form class="form-register" role="form" action="{{ URL::action('RegisterController@postGoon') }}" method="post">
  <h2 class="form-register-heading">Are you being sponsored?</h2>
  <p>{{ HTML::link(URL::to('register/goon-sponsored'), "Click here") }} to access the sponsor form.</p>

  <h2 class="form-register-heading">Fill out the following</h2>
  <p>Your Goon ID is your login name. Your Display Name can be changed later, but remember that your Goon ID is always visible.</p>
  <p>Make sure your e-mail is valid as you will receive your acceptance information through it.</p>
  @if (Session::has('error'))
    <div class="alert alert-danger">
    {{ Session::get('error') }}
    </div>
  @endif
  <input type="text" name="goonid" class="form-control" placeholder="Desired Goon ID" required="" autofocus="">
  <input type="email" name="email" class="form-control" placeholder="E-Mail Address" required="" autofocus="">
  <button class="btn btn-lg btn-primary btn-block" type="submit">Next</button>
</form>
@stop
