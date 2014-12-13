<?php

class Game extends Eloquent {
	protected $table = "Game";
	protected $primaryKey = "GID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo('User', 'GOwnerID');
	}

	public function gameusers() {
		return $this->hasMany('GameUser', 'GID');
	}

	public function notes() {
		return $this->belongsToMany('Note', 'GameHasNote', 'GID', 'NID');
	}

	public function organizations() {
		return $this->belongsToMany('GameOrganization', 'GameHasGameOrganization', 'GID', 'GOID');
	}
}
