<?php

class GroupSeeder extends Seeder {

	public function run()
	{
		DB::table('Group')->delete();

		$sa = Group::create(array(
			'GRName' => 'Something Awful',
			'GRLDAPName' => null	// SA
		));
	}
}
