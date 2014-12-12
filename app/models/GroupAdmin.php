<?php

class GroupAdmin extends Eloquent {
	protected $table = "GroupAdmin";
	protected $primaryKey = "GRAID";
	protected $timestamps = false;


	public function user() {
		return $this->hasOne('User', 'UID', 'UID');
	}

	public function role() {
		return $this->hasOne('Role', 'RID', 'RID');
	}
}
