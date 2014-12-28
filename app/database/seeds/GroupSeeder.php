<?php

class GroupSeeder extends Seeder {

	public function run()
	{
		DB::table('Group')->delete();

		$sa = Group::create(array(
			'GRCode' => 'SA',
			'GRName' => 'Something Awful',
			'GRLDAPGroup' => 'SA'
		));

		$frog = Group::create(array(
			'GRCode' => 'FROG',
			'GRName' => 'FROGSWARM',
			'GRLDAPGroup' => 'FROGSWARM'
		));
	}
}
