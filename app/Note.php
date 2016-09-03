<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
	protected $table = "Note";
	protected $primaryKey = "NID";
	public $timestamps = false;


	public function notetype() {
		return $this->belongsTo(NoteType::class, 'NTID');
	}

	public function user() {
		return $this->belongsTo(User::class, 'UID');
	}

	public function createdby() {
		return $this->belongsTo(User::class, 'NCreatedByUID');
	}

	public function groups() {
		return $this->belongsToMany(Group::class, 'GroupHasNote', 'NID', 'GRID');
	}

	public function gameorgs() {
		return $this->belongsToMany(GameOrg::class, 'GameOrgHasNote', 'NID', 'GOID');
	}
}
