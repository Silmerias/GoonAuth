<?php

namespace App;

// use App\Group;
// use App\Role;
// use App\User;
// use App\UserStatus;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserSeeder extends Seeder {

	public function run()
	{
		DB::table('User')->delete();

		$active = UserStatus::active()->first();
		$roll = Role::where('RName', 'Admin')->first();
		$sa = Group::first();

		$user = User::create(array(
			'USID' => $active->USID,
			'UEmail' => 'example@example.com',
			'UGoonID' => 'admin',
			'UGroup' => $sa->GRID,
			'ULDAPLogin' => 'admin',
			'USACachedName' => '<Admin Account>',
			'USAUserID' => 0
		));

		$user->ownedgroups()->save($sa);
		$user->grouproles()->attach($roll, array('GRID' => $sa->GRID));
	}
}
