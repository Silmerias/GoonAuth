<?php

class NoteType extends Eloquent {
	protected $table = "NoteType";
	protected $primaryKey = "NTID";
	public $timestamps = false;


	public function notes() {
		return $this->hasMany('Note', 'NTID');
	}

	public function roles() {
		return $this->belongsToMany('Role', 'RoleSeesNoteType', 'NTID', 'RID');
	}
}
