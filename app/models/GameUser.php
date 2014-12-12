<?php

class GameUser extends Eloquent {
	protected $table = "GameUser";
	protected $primaryKey = "GUID";
	protected $timestamps = false;


	public function game() {
		return $this->belongsTo('Game', 'GID', 'GID');
	}

	public function user() {
		return $this->belongsTo('User', 'UID', 'UID');
	}

	public function userstatus() {
		return $this->hasOne('UserStatus', 'USID', 'USID');
	}
}
