<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteType extends Model
{
	protected $table = "NoteType";
	protected $primaryKey = "NTID";
	public $timestamps = false;


	public function notes() {
		return $this->hasMany(Note::class, 'NTID');
	}

	public function roles() {
		return $this->belongsToMany(Role::class, 'RoleSeesNoteType', 'NTID', 'RID');
	}
}
