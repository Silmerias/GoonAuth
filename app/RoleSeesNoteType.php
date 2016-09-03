<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleSeesNoteType extends Model
{
	protected $table = "RollSeesNoteType";
	protected $primaryKey = "RSNTID";
	public $timestamps = false;


	public function role() {
		return $this->belongsTo(UserStatus::class, 'RID');
	}

	public function notetype() {
		return $this->belongsTo(NoteType::class, 'NTID');
	}
}
