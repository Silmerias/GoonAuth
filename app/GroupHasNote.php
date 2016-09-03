<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupHasNote extends Model
{
	protected $table = "GroupHasNote";
	protected $primaryKey = "GRHNID";
	public $timestamps = false;


	public function group() {
		return $this->belongsTo(Group::class, 'GRID');
	}

	public function note() {
		return $this->belongsTo(Note::class, 'NID');
	}
}
