<?php

class Group extends Eloquent {
	protected $table = "Group";
	protected $primaryKey = "GRID";
	protected $timestamps = false;


	public function owner() {
		return $this->hasOne('User', 'UID', 'GROwnerID');
	}
}
