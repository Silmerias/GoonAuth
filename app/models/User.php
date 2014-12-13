<?php

class User extends Eloquent {
	protected $table = "User";
	protected $primaryKey = "UID";
	public $timestamps = false;


	public function userstatus() {
		return $this->belongsTo('UserStatus', 'USID');
	}

	public function sponsoring() {
		return $this->belongsTo('User', 'USponsorID');
	}

	public function affilategroup() {
		return $this->belongsTo('Group', 'UAffiliateGroup');
	}

	public function notes() {
		return $this->hasMany('Note', 'UID');
	}

	public function sponsor() {
		return $this->hasOne('User', 'UID', 'USponsorID');
	}

	public function ownedgroups() {
		return $this->hasMany('Group', 'GROwnerID');
	}

	public function ownedgames() {
		return $this->hasMany('Game', 'GOwnerID');
	}

	public function ownedgameorganizations() {
		return $this->hasMany('GameOrganization', 'GOOwnerID');
	}

	public function grouproles() {
		return $this->belongsToMany('Role', 'GroupAdmin', 'UID', 'RID');
	}

	public function gameroles() {
		return $this->belongsToMany('Role', 'GameAdmin', 'UID', 'RID');
	}

	public function gameusers() {
		return $this->hasMany('GameUser', 'UID');
	}

	public function scopeSponsored($query) {
		return $query->whereNotNull('USponsorID');
	}
}
