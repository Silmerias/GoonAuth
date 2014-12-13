<?php

class GroupAdmin extends Eloquent {
	protected $table = "GroupAdmin";
	protected $primaryKey = "GRAID";
	public $timestamps = false;


	public function group() {
		return $this->belongsTo('Group', 'GRID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function role() {
		return $this->belongsTo('Role', 'RID');
	}
}
