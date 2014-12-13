<?php

class Note extends Eloquent {
	protected $table = "Note";
	protected $primaryKey = "NID";
	public $timestamps = false;


	public function notetype() {
		return $this->belongsTo('NoteType', 'NTID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function createdby() {
		return $this->belongsTo('User', 'NCreatedByUID');
	}

	public function groups() {
		return $this->belongsToMany('Group', 'GroupHasNote', 'NID', 'GRID');
	}

	public function games() {
		return $this->belongsToMany('Game', 'GameHasNote', 'NID', 'GID');
	}
}
