<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
	protected $table = "GameUser";
	protected $primaryKey = "GUID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo(Game::class, 'GID');
	}

	public function user() {
		return $this->belongsTo(User::class, 'UID');
	}

	public function gameorgs() {
		return $this->belongsToMany(GameOrg::class, 'GameOrgHasGameUser', 'GUID', 'GOID')
			->withPivot('USID');
	}
}
