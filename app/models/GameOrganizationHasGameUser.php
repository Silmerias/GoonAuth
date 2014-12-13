<?php

class GameOrganizationHasGameUser extends Eloquent {
	protected $table = "GameOrganizationHasGameUser";
	protected $primaryKey = "GOHGUID";
	public $timestamps = false;


	public function gameorganization() {
		return $this->belongsTo('GameOrganization', 'GOID');
	}

	public function gameuser() {
		return $this->belongsTo('GameUser', 'GUID');
	}
}
