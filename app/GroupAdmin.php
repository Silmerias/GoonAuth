<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupAdmin extends Model
{
	protected $table = "GroupAdmin";
	protected $primaryKey = "GRAID";
	public $timestamps = false;


	public function group() {
		return $this->belongsTo(Group::class, 'GRID');
	}

	public function user() {
		return $this->belongsTo(User::class, 'UID');
	}

	public function role() {
		return $this->belongsTo(Role::class, 'RID');
	}
}
