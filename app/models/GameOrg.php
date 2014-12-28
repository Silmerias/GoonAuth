<?php

class GameOrg extends Eloquent {
	protected $table = "GameOrg";
	protected $primaryKey = "GOID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo('User', 'GOOwnerID');
	}

	public function gameusers() {
		return $this->belongsToMany('GameUser', 'GameOrgHasGameUser', 'GOID', 'GUID')
			->withPivot('USID');
	}

	public function games() {
		return $this->belongsToMany('Game', 'GameHasGameOrg', 'GOID', 'GID');
	}

	public function notes() {
		return $this->belongsToMany('Note', 'GameOrgHasNote', 'GOID', 'NID');
	}

	public function sponsored() {
		return $this->belongsToMany('User', 'Sponsor', 'GOID', 'UID');
	}
}
