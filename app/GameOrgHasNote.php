<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameOrgHasNote extends Model
{
	protected $table = "GameOrgHasNote";
	protected $primaryKey = "GOHNID";
	public $timestamps = false;


	public function gameorg() {
		return $this->belongsTo(GameOrg::class, 'GOID');
	}

	public function note() {
		return $this->belongsTo(Note::class, 'NID');
	}
}
