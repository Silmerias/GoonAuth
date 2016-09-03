<?php

class Role extends Eloquent {
	protected $table = "Role";
	protected $primaryKey = "RID";
	public $timestamps = false;


	public function notetypes() {
		return $this->belongsToMany('NoteType', 'RoleSeesNoteType', 'RID', 'NTID');
	}

	public function groupusers() {
		return $this->belongsToMany('User', 'GroupAdmin', 'RID', 'UID');
	}

	public function gameorgusers() {
		return $this->belongsToMany('User', 'GameOrgAdmin', 'RID', 'UID');
	}
}