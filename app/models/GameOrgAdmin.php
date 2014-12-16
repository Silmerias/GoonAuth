<?php

class GameOrgAdmin extends Eloquent {
	protected $table = "GameOrgAdmin";
	protected $primaryKey = "GOAID";
	public $timestamps = false;


	public function gameorg() {
		return $this->belongsTo('GameOrg', 'GOID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function role() {
		return $this->belongsTo('Role', 'RID');
	}
}
