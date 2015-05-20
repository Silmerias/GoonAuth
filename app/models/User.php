<?php

use \Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface
{
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
		return $this->belongsToMany('User', 'Sponsor', 'SSponsoredID', 'UID');
	}

	public function sponsoring() {
		return $this->belongsToMany('User', 'Sponsor', 'UID', 'SSponsoredID');
	}

	public function canSponsor() {
		if (Config::get('goonauth.sponsors') == null)
			return true;

		if ($this->grouproles()->count() !== 0)
			return true;
		if ($this->gameorgroles()->count() !== 0)
			return true;

		if ($this->sponsoring->count() <= Config::get('goonauth.sponsors'))
			return true;

		return false;
	}

	//-----------------------------------------------------------------------//

	public function getAuthIdentifier()
	{
		return $this->UID;
	}

	public function getAuthPassword()
	{
		return null;
	}

	public function getRememberToken()
	{
		return $this->URememberToken;
	}

	public function setRememberToken($token)
	{
		$this->URememberToken = $token;
	}

	public function getRememberTokenName()
	{
		return "URememberToken";
	}
}
