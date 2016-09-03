@extends('layouts.main')
@section('content')
<h1>User Blacklist</h1>
<p><a href="{{ URL::to('admin') }}">Manage Users</a></p>
<div class="row">
    <div class="col-md-12">
        {{ Form::open(array('url' => 'admin/blacklist', 'method' => 'post', 'class' => 'form-inline')) }}
            <div class="form-group">
                <input type="text" name="sa_username" class="form-control" placeholder="SA Username">
            </div>
            <div class="form-group">
                <input type="text" name="reason" class="form-control" placeholder="Blacklist Reason" style="width: 400px;">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-danger">Add to Blacklist</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <table class="table">
        <thead>
            <th style="width: 75px;">ID</th>
            <th>SA Username</th>
            <th>Admin</th>
            <th>Date Added</th>
            <th>Reason</th>
            <th style="width: 150px">Controls</th>
        </thead>
        @foreach ($blacklist as $user)
        <tr>
            <td><span class="label label-success">{{ $user->id }}</span></td>
            <td>{{ $user->sa_username }}</td>
            <td>{{ $user->admin->sa_username }}</td>
            <td>{{ $user->created_at }}</td>
            <td>{{ $user->reason }}</td>
            <td>{{ Form::open(array('url' => 'admin/blacklist/'. $user->id, 'method' => 'delete')) }}<button class="btn btn-danger btn-xs">Delete</button></form></td>
        </tr>
        @endforeach
    </table>
    {{ $blacklist->links(); }}
    </div>
</div>
@stop