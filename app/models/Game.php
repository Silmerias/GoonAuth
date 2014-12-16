<?php

class Game extends Eloquent {
	protected $table = "Game";
	protected $primaryKey = "GID";
	public $timestamps = false;


	public function gameusers() {
		return $this->hasMany('GameUser', 'GID');
	}

	public function orgs() {
		return $this->belongsToMany('GameOrg', 'GameHasGameOrg', 'GID', 'GOID');
	}
}
