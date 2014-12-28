<?php

class Sponsor extends Eloquent {
	protected $table = "Sponsor";
	protected $primaryKey = "SID";
	public $timestamps = false;


	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function sponsor() {
		return $this->belongsTo('User', 'SSponsorID');
	}

	public function group() {
		return $this->belongsTo('Group', 'GRID');
	}

	public function gameorg() {
		return $this->belongsTo('GameOrg', 'GOID');
	}
}
