<?php

class GameHasNote extends Eloquent {
	protected $table = "GameHasNote";
	protected $primaryKey = "GHNID";
	protected $timestamps = false;


	public function group() {
		return $this->hasOne('Game', 'GID', 'GID');
	}

	public function note() {
		return $this->hasOne('Note', 'NID', 'NID');
	}
}
