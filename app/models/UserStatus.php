<?php

class UserStatus extends Eloquent {
	protected $table = "UserStatus";
	protected $primaryKey = "USID";
	protected $timestamps = false;


	public function user() {
		return $this->belongsTo('User', 'USID', 'USID');
	}
}
