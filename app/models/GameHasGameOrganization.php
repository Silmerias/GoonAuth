<?php

class GameHasGameOrganization extends Eloquent {
	protected $table = "GameHasGameOrganization";
	protected $primaryKey = "GHGOID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo('Game', 'GID');
	}

	public function gameorganization() {
		return $this->belongsTo('GameOrganization', 'GOID');
	}
}
