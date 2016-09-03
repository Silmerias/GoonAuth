<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameOrgHasGameUser extends Model
{
	protected $table = "GameOrgHasGameUser";
	protected $primaryKey = "GOHGUID";
	public $timestamps = false;


	public function userstatus() {
		return $this->belongsTo(UserStatus::class, 'USID');
	}

	public function gameorg() {
		return $this->belongsTo(GameOrg::class, 'GOID');
	}

	public function gameuser() {
		return $this->belongsTo(GameUser::class, 'GUID');
	}
}
