<?php

class GameHasGameOrg extends Eloquent {
	protected $table = "GameHasGameOrg";
	protected $primaryKey = "GHGOID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo('Game', 'GID');
	}

	public function gameorg() {
		return $this->belongsTo('GameOrg', 'GOID');
	}
}
