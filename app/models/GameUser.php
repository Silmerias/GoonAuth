<?php

class GameUser extends Eloquent {
	protected $table = "GameUser";
	protected $primaryKey = "GUID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo('Game', 'GID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function gameorgs() {
		return $this->belongsToMany('GameOrg', 'GameOrgHasGameUser', 'GUID', 'GOID')
			->withPivot('USID');
	}
}
