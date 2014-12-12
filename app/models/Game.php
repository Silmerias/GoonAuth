<?php

class Game extends Eloquent {
	protected $table = "Game";
	protected $primaryKey = "GID";
	protected $timestamps = false;


	public function owner() {
		return $this->hasOne('User', 'UID', 'GOwnerID');
	}
}
