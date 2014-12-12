<?php

class GroupHasNote extends Eloquent {
	protected $table = "GroupHasNote";
	protected $primaryKey = "GRHNID";
	protected $timestamps = false;


	public function group() {
		return $this->hasOne('Group', 'GRID', 'GRID');
	}

	public function note() {
		return $this->hasOne('Note', 'NID', 'NID');
	}
}
