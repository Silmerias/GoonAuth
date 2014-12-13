<?php

class GameHasNote extends Eloquent {
	protected $table = "GameHasNote";
	protected $primaryKey = "GHNID";
	public $timestamps = false;


	public function group() {
		return $this->belongsTo('Game', 'GID');
	}

	public function note() {
		return $this->belongsTo('Note', 'NID');
	}
}
