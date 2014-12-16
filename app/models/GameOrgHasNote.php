<?php

class GameOrgHasNote extends Eloquent {
	protected $table = "GameOrgHasNote";
	protected $primaryKey = "GOHNID";
	public $timestamps = false;


	public function gameorg() {
		return $this->belongsTo('GameOrg', 'GOID');
	}

	public function note() {
		return $this->belongsTo('Note', 'NID');
	}
}
