<?php

class GameAdmin extends Eloquent {
	protected $table = "GameAdmin";
	protected $primaryKey = "GAID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo('Game', 'GID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function role() {
		return $this->belongsTo('Role', 'RID');
	}
}
