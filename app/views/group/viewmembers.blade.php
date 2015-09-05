@extends('layouts.main')
@section('content')

<?php $auth = Auth::user(); ?>

<a class="label label-info" href="{{ URL::to('/') }}">Back to Home</a>

<h1>Group Member List</h1>
<div class="row">
	<div class="col-md-12">

	<?php $statuses = UserStatus::get(); ?>

	<div class="panel-group" id="filter" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="headingFilter">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#filter" href="#collapseFilter" aria-expanded="true" aria-controls="collapseFilter">
						Filters
					</a>
				</h4>
			</div>
			<div id="collapseFilter" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFilter">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-3"><p>Goon ID</p></div>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-btn">
										<select id="filter-goonid-searchby" class="selectpicker" data-container="body" data-width="120px">
											<option value="contains">contains</option>
											<option value="starts">starts with</option>
										</select>
									</div>
									<input id="filter-goonid" type="text" class="form-control" aria-label="...">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-3"><p>SA name</p></div>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-btn">
										<select id="filter-sa-searchby" class="selectpicker" data-container="body" data-width="120px">
											<option value="contains">contains</option>
											<option value="starts">starts with</option>
										</select>
									</div>
									<input id="filter-sa" type="text" class="form-control" aria-label="...">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-3"><p>Status</p></div>
							<div class="col-md-9">
								<select id="filter-status" class="selectpicker" data-container="body" data-width="100%" multiple data-selected-text-format="count>3">
									@foreach (UserStatus::get() as $status)
									<option value="{{ $status->USID }}">{{ $status->USStatus }}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-3"><p>Reg Date</p></div>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-btn">
										<select id="filter-regdate-searchby" class="selectpicker" data-container="body" data-width="120px">
											<option value="gte" title=">=">&gt;=</option>
											<option value="lte" title="<=">&lt;=</option>
											<option value="e">=</option>
										</select>
									</div>
									<input id="filter-regdate" class="form-control date" aria-label="...">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="col-md-3"><p>Order By</p></div>
							<div class="col-md-9">
								<div class="input-group">
									<div class="input-group-btn">
										<select id="filter-orderby" class="selectpicker" data-container="body" data-width="100%">
											<option value="goonid">Goon ID</option>
											<option value="status">Status</option>
											<option value="regdate">Reg Date</option>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="col-md-offset-3 col-md-3">
								<button type="button" id="filter-apply" class="btn btn-success">Apply</button>
							</div>
							<div class="col-md-6">
								<button type="button" id="filter-reset" class="btn btn-danger">Reset</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@if ($members->count() == 0)

	<p>No members found.  Either this group has no members or your filter returned 0 results.</p>

	@else

	<table class="table">
		<thead>
			<th style="width: 100px;">Status</th>
			<th style="width: 150px;">Goon ID</th>
			<th>SA Name</th>
			<th>Sponsor</th>
			<th style="width: 125px;">SA Reg Date</th>
			<th style="width: 90px;">SA Posts</th>
			<th style="width: 40px;"></th>
			<th style="width: 150px;">Actions</th>
		</thead>
		@foreach ($members as $user)
		<tr id="ID_{{ $user->UID }}">
			<td>
				{{ e($statuses[$user->USID - 1]->USStatus) }}
			</td>
			<td><a href="{{ URL::to('user/'.$user->UID) }}" target="_blank">{{ e($user->UGoonID) }}</a></td>
			<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->USACachedName) }}">{{ e($user->USACachedName) }}</a></td>
			@if (is_null($user->sponsor))
				<td></td>
			@else
				<td><a href="http://forums.somethingawful.com/member.php?action=getinfo&amp;username={{ urlencode($user->sponsor->USACachedName) }}">{{ e($user->sponsor->USACachedName) }}</a></td>
			@endif
			<td>{{ $user->USARegDate }}</td>
			<td>{{ $user->USACachedPostCount }}</td>
			<td>
				<button type="button" class="btn btn-note" data-toggle="popover" data-uid="{{ $user->UID }}">
					<span class="glyphicon glyphicon-envelope" aria-hidden="true" style="color: goldenrod"></span>
				</button>
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-Kick" data-gid="{{ $user->UID }}">Kick</button>
				<button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modal-Note" data-goonid="{{ e($user->UGoonID) }}" data-uid="{{ $user->UID }}" data-url="{{ URL::to(Request::path()) }}">Add Note</button>
			</td>
		</tr>
		@endforeach
	</table>

	{{ $members->links(); }}

	@endif

	</div>
</div>

<div class="modal fade" id="modal-Error" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Error</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger">
					<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					<span class="sr-only">Error:</span>
					<span class="error"></span>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-Note" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add Note</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label for="note-type" class="control-label">Note Type:</label>
					<select id="note-type" class="form-control">
					@foreach (NoteType::with(array('roles.groupusers' => function($q) use($auth, $group) { $q->where('GroupAdmin.UID', $auth->UID)->where('GroupAdmin.GRID', $group->GRID); }))->where('NTSystemUseOnly', 'false')->get() as $nt)
						<option value="{{ $nt->NTID }}" color="{{ $nt->NTColor }}">{{ $nt->NTName }}</option>
					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="note-subject" class="control-label">Subject:</label>
					<input type="text" id="note-subject" class="form-control" placeholder="Note subject (optional)"></textarea>
				</div>
				<div class="form-group">
					<label for="note-text" class="control-label">Message:</label>
					<textarea id="note-text" class="form-control" placeholder="Type your note here"></textarea>
				</div>
				<div class="input-group">
					<span class="input-group-addon">
						<input id="note-global" type="checkbox">
					</span>
					<label for="note-global" class="form-control text-sm">Global note?</label>
				</div>
			</div>

			<div class="form-group">
				<div id="note-preview" class="note" style="background-color: white">
					<p class="note-header">
						<span class="note-type">General</span>
						<span class="note-user">- System</span>
						<span class="note-global">[Global]</span>
					</p>
					<p class="note-subject"></p>
					<p class="note-comment"></p>
					<p class="note-footer">By 
						<a href="{{ URL::to('user/'.$auth->UID) }}" target="_blank">{{ e($auth->UGoonID) }}</a>
						- {{ Carbon::now()->toDateTimeString() }}
					</p>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" id="note-add" class="btn btn-primary">Add Note</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
{{ HTML::script('assets/js/goonauth-addnote.js') }}

<script>

$(document).ready(function() {
	var expand = false;
	var params = getQueryParams(document.location.search);
	if (params['goonid-by'] !== undefined)	{ expand = true; $('#filter-goonid-searchby').val(params['goonid-by']); }
	if (params['sa'] !== undefined)			{ expand = true; $('#filter-sa').val(params['sa']); }
	if (params['sa-by'] !== undefined)		{ expand = true; $('#filter-sa-searchby').val(params['sa-by']); }
	if (params['regdate'] !== undefined)	{ expand = true; $('#filter-regdate').val(params['regdate']); }
	if (params['regdate-by'] !== undefined)	{ expand = true; $('#filter-regdate-searchby').val(params['regdate-by']); }
	if (params['status'] !== undefined)
	{
		expand = true;
		var statuses = params['status'].split(',');
		$('#filter-status').val(statuses);
		$('#filter-status').selectpicker('render');
	}
	if (params['goonid'] !== undefined)		{ expand = true; $('#filter-goonid').val(params['goonid']); }
	if (params['orderby'] !== undefined)	{ expand = true; $('#filter-orderby').val(params['orderby']); }

	$('#filter-regdate').datepicker();

	if (expand === true)
		$('#collapseFilter').collapse('show');
});

function error(msg)
{
	$('#modal-Error .modal-body .error').text(msg);
	$('#modal-Error').modal('show');
}

$('[data-toggle="popover"]').click(function() {
	var d = $(this);
	var uid = d.data('uid');

	d.off('click');
	d.off('mouseenter mouseleave');

	$.ajax({
		url: '/user/'+uid+'/notes',
		type: 'get'
	})
	.done(function(ret) {
		d.popover({
			trigger: 'focus',
			html: 'true',
			placement: 'left',
			template: '<div class="popover note-popover"><div class="popover-content"></div></div>',
			content: ret
		}).popover('show');
	});
});

$('#filter-apply').click(function() {
	var filters = '';
	var goonid = $('#filter-goonid').val();
	var goonid_by = $('#filter-goonid-searchby').val();
	var sa = $('#filter-sa').val();
	var sa_by = $('#filter-sa-searchby').val();
	var statuses = $('#filter-status').selectpicker('val');
	var regdate = $('#filter-regdate').val();
	var regdate_by = $('#filter-regdate-searchby').val();
	var orderby = $('#filter-orderby').val();

	if (goonid.length !== 0)
		filters += '&goonid=' + encodeURIComponent(goonid) + '&goonid-by=' + encodeURIComponent(goonid_by);
	if (sa.length !== 0)
		filters += '&sa=' + encodeURIComponent(sa) + '&sa-by=' + encodeURIComponent(sa_by);
	if (statuses !== null && statuses.length !== 0)
	{
		filters += '&status=';
		for (var s in statuses)
			filters += statuses[s] + ',';
		filters = filters.substr(0, filters.length - 1);
	}
	if (regdate.length !== 0)
		filters += '&regdate=' + encodeURIComponent(regdate) + '&regdate-by=' + encodeURIComponent(regdate_by);
	if (orderby.length !== 0)
		filters += '&orderby=' + encodeURIComponent(orderby);

	if (filters.length !== 0)
		window.location.href = '{{ URL::to(Request::path()) }}' + '?' + filters.substr(1);
	else window.location.href = '{{ URL::to(Request::path()) }}';
});

$('#filter-reset').click(function() {
	window.location.href = '{{ URL::to(Request::path()) }}';
});

</script>

@stop
