<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
	protected $table = "Sponsor";
	protected $primaryKey = "SID";
	public $timestamps = false;


	public function user() {
		return $this->belongsTo(User::class, 'UID');
	}

	public function sponsored() {
		return $this->belongsTo(User::class, 'SSponsoredID');
	}
}
