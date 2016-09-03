@extends('layouts.main')
@section('content')
<h1>Forum Name: {{ $user->xf_username }}<br>
<small>SA Username: {{ !empty($user->sa_username) ? $user->sa_username : "N/A" }}</small></h1>
<div class="row">
    <div class="col-md-12">
    <p><strong>Account Status:</strong> 
    @if ($user->is_banned)
        <span class="label label-danger">Banned</span>
    @endif
    @if (!empty($user->sa_username))
        <span class="label label-success">Authed</span>
    @elseif ($user->is_sponsored)
        <span class="label label-info">Sponsored</span> by <strong>{{ HTML::link('admin/user/'.$user->sponsoree()->first()->id, $user->sponsoree()->first()->sa_username) }}</strong>
    @else
        <span class="label label-default">Unauthed</span>
    @endif
    </p>
    @if (in_array(Session::get('auth')->id, Config::get('goonauth.superAdmins')))
        @if ($user->is_banned)
            <p><a href="{{ URL::to('admin/user/'.$user->id.'/ban') }}" class="btn btn-xs btn-success">Unban User</a></p>
        @else
            <p><a href="{{ URL::to('admin/user/'.$user->id.'/ban') }}" class="btn btn-xs btn-danger">Ban User</a></p>
        @endif
    @endif
    @if (!empty($user->sa_username) || $user->is_sponsored)
    <p><strong>Linked:</strong> {{ Carbon::createFromFormat('Y-m-d H:i:s', $user->linked_at)->toDateString() }}
    @endif
    @if (!empty($user->sa_username))
    <p><strong>SA Profile:</strong> {{ HTML::link('http://forums.somethingawful.com/member.php?action=getinfo&username='.$user->sa_username, 'Click Here', array('target' => '_blank')) }}</p>
    @endif
    <p><strong>Characters:</strong></p>
    <ul>
    @foreach ($user->characters as $character)
        <li>{{ $character->locked ? '<i class="fa fa-lock"></i>' : '<i class="fa fa-unlock"></i>' }} <a href="{{ URL::to('admin/character/lock/'.$character->id) }}">{{ $character->name }}</a> {{ $character->is_main ? '<span class="label label-primary">Main</span>' : '' }}</li>
    @endforeach
    </ul>
    @if (!empty($user->sa_username))
    <p><strong>Sponsored Accounts:</strong></p>
    <ul>
    @foreach ($user->sponsors as $sponsor)
        <li>{{ HTML::link('admin/user/'.$sponsor->id, $sponsor->xf_username) }}</li>
    @endforeach
    </ul>
    @endif
    @if (in_array(Session::get('auth')->id, Config::get('goonauth.superAdmins')))
        @if (!$user->is_admin)
        <p><a href="{{ URL::to('admin/user/'. $user->id . '?admin=1') }}" class="label label-primary">Set as admin</a></p>
        @else
        <p><a href="{{ URL::to('admin/user/'. $user->id . '?admin=0') }}" class="label label-danger">Remove Admin</a></p>
        @endif
    @endif
    <p><strong>Admin Notes:</strong></p>
    <ul>
    @foreach ($user->notes as $note)
        <li>{{ Form::open(array('url' => 'admin/note/'. $note->id, 'method' => 'delete')) }}<span class="label label-primary">{{ $note->admin->sa_username }}</span> <span class="label label-default">{{ $note->created_at }}</span> {{ $note->note }} <button class="btn btn-danger btn-xs">Delete</button></form></li>
    @endforeach
    </ul>
    <form action="{{ URL::to('admin/note') }}" method="post">
        <input type="hidden" name="auth_id" value="{{ $user->id }}">
        <div class="input-group">
            <input type="text" class="form-control" id="note" name="note" placeholder="Add a new note...">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="submit">Save</button>
              </span>
          </div>
    </form>
    </div>

</div>
@stop