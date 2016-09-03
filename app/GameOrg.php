<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameOrg extends Model
{
	protected $table = "GameOrg";
	protected $primaryKey = "GOID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo(User::class, 'GOOwnerID');
	}

	public function gameusers() {
		return $this->belongsToMany(GameUser::class, 'GameOrgHasGameUser', 'GOID', 'GUID')
			->withPivot('USID');
	}

	public function games() {
		return $this->belongsToMany(Game::class, 'GameHasGameOrg', 'GOID', 'GID');
	}

	public function notes() {
		return $this->belongsToMany(Note::class, 'GameOrgHasNote', 'GOID', 'NID');
	}

	public function sponsored() {
		return $this->belongsToMany(User::class, 'Sponsor', 'GOID', 'UID');
	}
}
