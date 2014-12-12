<?php

class Note extends Eloquent {
	protected $table = "Note";
	protected $primaryKey = "NID";
	protected $timestamps = false;


	public function user() {
		return $this->belongsTo('User', 'UID', 'UID');
	}

	public function createdby() {
		return $this->belongsTo('User', 'UID', 'NCreatedByUID');
	}

	public function notetype() {
		return $this->hasOne('NoteType', 'NTID', 'NTID');
	}
}
