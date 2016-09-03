<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
	protected $table = "Game";
	protected $primaryKey = "GID";
	public $timestamps = false;


	public function gameusers() {
		return $this->hasMany(GameUser::class, 'GID');
	}

	public function orgs() {
		return $this->belongsToMany(GameOrg::class, 'GameHasGameOrg', 'GID', 'GOID');
	}
}
