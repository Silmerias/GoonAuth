<?php

class GameOrgHasGameUser extends Eloquent {
	protected $table = "GameOrgHasGameUser";
	protected $primaryKey = "GOHGUID";
	public $timestamps = false;


	public function userstatus() {
		return $this->belongsTo('UserStatus', 'USID');
	}

	public function gameorg() {
		return $this->belongsTo('GameOrg', 'GOID');
	}

	public function gameuser() {
		return $this->belongsTo('GameUser', 'GUID');
	}
}
