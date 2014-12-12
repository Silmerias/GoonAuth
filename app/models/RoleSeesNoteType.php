<?php

class RoleSeesNoteType extends Eloquent {
	protected $table = "RollSeesNoteType";
	protected $primaryKey = "RSNTID";
	protected $timestamps = false;


	public function role() {
		return $this->belongsTo('UserStatus', 'RID', 'RID');
	}

	public function notetype() {
		return $this->hasOne('NoteType', 'NTID', 'NTID');
	}
}
