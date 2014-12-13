@extends('layouts.main')
@section('content')
<h1>Sponsor List</h1>
<div class="row">
    <div class="col-md-12">
    @if ($auth->sponsoring()->count() == 0)
    <p>You currently have no sponsored accounts registered.</p>
    <!-- <p><a href="{{ URL::to('sponsor/add') }}" class="btn btn-success">Sponsor a Friend</a></p> -->
    @else
    <p>Your registered sponsors are below. You currently have <strong>{{ $auth->sponsoring()->count() }}</strong> sponsors registered.</p>  
    <table class="table">
        <thead>
            <th>Sponsor Name</th>
        </thead>
        @foreach ($auth->sponsoring()->get() as $sponsor)
        <tr>
            <td>{{ $sponsor->UGoonID }}</td>
        </tr>
        @endforeach
    </table>
    @if ($auth->sponsoring()->count() >= Config::get('goonauth.sponsors'))
    <p><a href="#" class="btn btn-success" disabled>Max Sponsors Reached</a></p>
    @else
    <!-- <p><a href="{{ URL::to('sponsor/add') }}" class="btn btn-success">Add Sponsor</a></p> -->
    @endif
    @endif
    </div>
</div>
@stop
