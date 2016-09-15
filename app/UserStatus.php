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

	public function scopeInactive($query) {
		return $query->where('USCode', '<>', 'ACTI')
			>where('USCode', '<>', 'VACA');
	}

	public static function unknown() {
		return UserStatus::where('USCode', 'UNKN')->first();
	}

	public static function pending() {
		return UserStatus::where('USCode', 'PEND')->first();
	}

	public static function rejected() {
		return UserStatus::where('USCode', 'REJE')->first();
	}

	public static function active() {
		return UserStatus::where('USCode', 'ACTI')->first();
	}

	public static function vacation() {
		return UserStatus::where('USCode', 'VACA')->first();
	}

	public static function probation() {
		return UserStatus::where('USCode', 'PROB')->first();
	}

	public static function banned() {
		return UserStatus::where('USCode', 'BAN')->first();
	}
}
