<?php

class Sponsor extends Eloquent {
	protected $table = "Sponsor";
	protected $primaryKey = "SID";
	public $timestamps = false;


	public function user() {
		return $this->belongsTo('User', 'UID');
	}

	public function sponsored() {
		return $this->belongsTo('User', 'SSponsoredID');
	}
}
