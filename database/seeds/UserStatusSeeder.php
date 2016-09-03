<?php

namespace App;

// use App\UserStatus;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserStatusSeeder extends Seeder {

	public function run()
	{
		DB::table('User')->delete();
		DB::table('UserStatus')->delete();

		UserStatus::create(array('USCode' => 'UNKN', 'USStatus' => 'Unknown'));
		UserStatus::create(array('USCode' => 'PEND', 'USStatus' => 'Pending Verification'));
		UserStatus::create(array('USCode' => 'REJE', 'USStatus' => 'Rejected'));
		UserStatus::create(array('USCode' => 'ACTI', 'USStatus' => 'Active'));
		UserStatus::create(array('USCode' => 'VACA', 'USStatus' => 'Vacation'));
		UserStatus::create(array('USCode' => 'PROB', 'USStatus' => 'Probation'));
		UserStatus::create(array('USCode' => 'BAN', 'USStatus' => 'Banned'));
	}

}
