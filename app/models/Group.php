<?php

class Group extends Eloquent {
	protected $table = "Group";
	protected $primaryKey = "GRID";
	public $timestamps = false;


	public function owner() {
		return $this->belongsTo('User', 'GROwnerID');
	}

	public function notes() {
		return $this->belongsToMany('Note', 'GroupHasNote', 'GRID', 'NID');
	}
}
