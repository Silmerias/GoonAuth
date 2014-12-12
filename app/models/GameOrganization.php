<?php

class GameOrganization extends Eloquent {
	protected $table = "GameOrganization";
	protected $primaryKey = "GOID";
	protected $timestamps = false;


	public function owner() {
		return $this->hasOne('User', 'UID', 'GOOwnerID');
	}
}
