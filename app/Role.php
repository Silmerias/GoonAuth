<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	protected $table = "Role";
	protected $primaryKey = "RID";
	public $timestamps = false;


	public function notetypes() {
		return $this->belongsToMany(NoteType::class, 'RoleSeesNoteType', 'RID', 'NTID');
	}

	public function groupusers() {
		return $this->belongsToMany(User::class, 'GroupAdmin', 'RID', 'UID');
	}

	public function gameorgusers() {
		return $this->belongsToMany(User::class, 'GameOrgAdmin', 'RID', 'UID');
	}
}
