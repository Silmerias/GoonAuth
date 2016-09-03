<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameOrgAdmin extends Model
{
	protected $table = "GameOrgAdmin";
	protected $primaryKey = "GOAID";
	public $timestamps = false;


	public function gameorg() {
		return $this->belongsTo(GameOrg::class, 'GOID');
	}

	public function user() {
		return $this->belongsTo(User::class, 'UID');
	}

	public function role() {
		return $this->belongsTo(Role::class, 'RID');
	}
}
