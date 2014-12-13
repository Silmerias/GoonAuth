<?php

class GroupHasNote extends Eloquent {
	protected $table = "GroupHasNote";
	protected $primaryKey = "GRHNID";
	public $timestamps = false;


	public function group() {
		return $this->belongsTo('Group', 'GRID');
	}

	public function note() {
		return $this->belongsTo('Note', 'NID');
	}
}
