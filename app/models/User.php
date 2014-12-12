<?php

class User extends Eloquent {
	protected $table = "User";
	protected $primaryKey = "UID";
	protected $timestamps = false;


	public function userstatus() {
		return $this->hasOne('UserStatus', 'USID', 'USID');
	}

	public function sponsor() {
		return $this->hasOne('User', 'UID', 'USponsorID');
	}

	public function sponsoring() {
		return $this->hasMany('User', 'USponsorID', 'UID');
	}

	public function affilategroup() {
		return $this->hasOne('Group', 'GRID', 'UAffiliateGroup');
	}

	public function scopeSponsored($query) {
		return $query->whereNotNull('USponsorID');
	}
}
