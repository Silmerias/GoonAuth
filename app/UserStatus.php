<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
	protected $table = "UserStatus";
	protected $primaryKey = "USID";
	public $timestamps = false;


	public function users() {
		return $this->hasMany(User::class, 'USID');
	}

	public function scopeActive($query) {
		return $query->where('USCode', 'ACTI');
	}

	public function scopeInactive($query) {
		return $query->where('USCode', '<>', 'ACTI')
			>where('USCode', '<>', 'VACA');
	}

	public function scopePending($query) {
		return $query->where('USCode', 'PEND');
	}

	public function scopeVacation($query) {
		return $query->where('USCode', 'VACA');
	}

	public function scopeBanned($query) {
		return $query->where('USCode', 'BAN');
	}
}
