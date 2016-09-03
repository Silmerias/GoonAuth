<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameHasGameOrg extends Model
{
	protected $table = "GameHasGameOrg";
	protected $primaryKey = "GHGOID";
	public $timestamps = false;


	public function game() {
		return $this->belongsTo(Game::class, 'GID');
	}

	public function gameorg() {
		return $this->belongsTo(GameOrg::class, 'GOID');
	}
}
