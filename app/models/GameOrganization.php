<?php

class GameOrganization extends Eloquent {
	protected $table = "GameOrganization";
	protected $primaryKey = "GOID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo('User', 'GOOwnerID');
	}

	public function gameusers() {
		return $this->belongsToMany('GameUser', 'GameOrganizationHasGameUser', 'GOID', 'GUID');
	}
}
