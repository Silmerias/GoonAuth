<?php

class User extends Eloquent {
	protected $table = "User";
	protected $primaryKey = "UID";
	public $timestamps = false;


	public function userstatus() {
		return $this->belongsTo('UserStatus', 'USID');
	}

	public function group() {
		return $this->belongsTo('Group', 'UGroup');
	}

	public function notes() {
		return $this->hasMany('Note', 'UID');
	}

	public function ownedgroups() {
		return $this->hasMany('Group', 'GROwnerID');
	}

	public function ownedgameorgs() {
		return $this->hasMany('GameOrg', 'GOOwnerID');
	}

	public function grouproles() {
		return $this->belongsToMany('Role', 'GroupAdmin', 'UID', 'RID')->withPivot('GRID');
	}

	public function gameorgroles() {
		return $this->belongsToMany('Role', 'GameOrgAdmin', 'UID', 'RID')->withPivot('GOID');
	}

	public function games() {
		return $this->belongsToMany('Game', 'GameUser', 'UID', 'GID');
	}

	public function gameusers() {
		return $this->hasMany('GameUser', 'UID');
	}

	public function sponsors() {
		return $this->belongsToMany('User', 'Sponsor', 'UID', 'SSponsorID');
	}

	public function sponsoring() {
		return $this->belongsToMany('User', 'Sponsor', 'SSponsorID', 'UID');
	}
}
