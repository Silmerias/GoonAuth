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

	public function gameorgs() {
		return $this->belongsToMany('Game', 'GameOrgHasNote', 'NID', 'GOID');
	}
}
