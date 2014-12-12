<?php

class GameAdmin extends Eloquent {
	protected $table = "GameAdmin";
	protected $primaryKey = "GAID";
	protected $timestamps = false;


	public function user() {
		return $this->hasOne('User', 'UID', 'UID');
	}

	public function role() {
		return $this->hasOne('Role', 'RID', 'RID');
	}
}
