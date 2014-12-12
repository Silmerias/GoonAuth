<?php

class GameOrganizationHasGameUser extends Eloquent {
	protected $table = "GameOrganizationHasGameUser";
	protected $primaryKey = "GOHGUID";
	protected $timestamps = false;


	public function gameorganization() {
		return $this->belongsTo('GameOrganization', 'GOID', 'GOID');
	}

	public function gameuser() {
		return $this->hasOne('GameUser', 'GUID', 'GUID');
	}
}
