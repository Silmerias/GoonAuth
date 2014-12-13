<?php

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
			'UGoonID' => Config::get('goonauth.adminAccount'),
			'UGroup' => $sa->GRID,
			'ULDAPLogin' => Config::get('goonauth.adminAccount')
		));

		$user->ownedgroups()->save($sa);
		$user->grouproles()->attach($roll, array('GRID' => $sa->GRID));
	}
}
