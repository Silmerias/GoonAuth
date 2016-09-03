<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
	protected $table = "Group";
	protected $primaryKey = "GRID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo(User::class, 'GROwnerID');
	}

	public function notes() {
		return $this->belongsToMany(Note::class, 'GroupHasNote', 'GRID', 'NID');
	}

	public function members() {
		return $this->hasMany(User::class, 'UGroup');
	}

	public function sponsored() {
		return $this->belongsToMany(User::class, 'Sponsor', 'GRID', 'UID');
	}

	public function supervisor() {
		return $this->belongsTo(Group::class, 'GRSupervisorID');
	}
}
