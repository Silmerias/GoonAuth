<?php

class RoleSeesNoteType extends Eloquent {
	protected $table = "RollSeesNoteType";
	protected $primaryKey = "RSNTID";
	public $timestamps = false;


	public function role() {
		return $this->belongsTo('UserStatus', 'RID');
	}

	public function notetype() {
		return $this->belongsTo('NoteType', 'NTID');
	}
}
