<div id="note-list">
@foreach ($notes as $note)
	<div class="note" style="background-color: {{ $note->NTColor }}">
		<p class="note-header">
			<span class="note-type">{{ e($note->NTName) }}</span>
			<span class="note-user">- {{ e($note->UGoonID) }}</span>
			@if ($note->NGlobal == true)
				<span class="note-global">[Global]</span>
			@endif
		</p>
		<p class="note-subject">{{ str_replace("\n", '<br>', e($note->NSubject)) }}</p>
		<p class="note-comment">{{ str_replace("\n", '<br>', e($note->NMessage)) }}</p>
		<p class="note-footer">By
			@if (is_null($note->CreatedGoonID))
				System
			@else
				<a href="{{ URL::to('user/'.$note->CreatedUID) }}" target="_blank">{{ e($note->CreatedGoonID) }}</a>
			@endif
			- {{ with(new Carbon($note->NTimestamp))->toDateTimeString() }}
		</p>
	</div>
@endforeach
</div>
