@extends('layouts.main')
@section('content')
<h1>Auth List</h1>
<p><a href="{{ URL::to('admin/blacklist') }}">Manage Blacklist</a></p>
<p><b>Total Accounts:</b> <span class="badge">{{ User::count() }}</span> <b>Total Authed Goons:</b> <span class="badge">{{ User::authed()->count() }}</span> <b>Total Sponsored:</b> <span class="badge">{{ User::sponsored()->count() }}</span> <b>Total Characters:</b> <span class="badge">{{ Character::count() }}</span></p>
<div class="row">
    <div class="col-md-3">
        <form action="{{ URL::to('admin') }}" method="post" class="form">
            {{ csrf_field() }}
            <div class="input-group">
              <input type="text" name="search" class="form-control" value="{{ $search }}">
              <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">Search</button>
              </span>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <table class="table">
        <thead>
            <th style="width: 75px;">Status</th>
            <th>SA Username</th>
            <th>Forum Username</th>
            <th>Characters</th>
            <th style="width: 150px">Controls</th>
        </thead>
        @foreach ($users as $user)
        <tr>
            @if ($user->is_banned)
                <td><span class="label label-danger">Banned</span></td>
            @elseif (!empty($user->sa_username))
                <td><span class="label label-success">Goon</span></td>
            @elseif ($user->is_sponsored)
                <td><span class="label label-info">Sponsored</span></td>
            @else
                <td><span class="label label-default">Unauthed</span></td>
            @endif
            @if (empty($user->sa_username))
                <td>N/A</td>
            @else
                <td>{{ $user->sa_username }}</td>
            @endif
            <td>{{ $user->xf_username }}</td>
            <td>
                @if ($user->characters->count() == 0)
                None
                @else
                    @foreach ($user->characters as $character)
                    <a href="{{ URL::to('admin/character/lock/'.$character->id) }}" class="label {{ $character->is_main ? 'label-primary' : 'label-default' }}">{{ $character->locked ? '<i class="fa fa-lock"></i>' : '<i class="fa fa-unlock"></i>' }} {{ $character->name }}</a>
                    @endforeach
                @endif
            <td><a href="{{ URL::to('admin/user/'.$user->id) }}" class="btn btn-primary btn-xs">View User</a></td>
        </tr>
        @endforeach
    </table>
    {{ $users->appends(array('search' => $search))->links(); }}
    </div>
</div>
<p><b>Admins:</b> 
@foreach (User::where('is_admin', 1)->get() as $user)
<a href="{{ URL::to('admin/user/'.$user->id) }}" class="label label-default">{{ $user->sa_username }}</a>
@endforeach
</p>
@stop