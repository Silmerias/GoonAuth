<?php

namespace App;

// use App\Group;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

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
			'GRName' => 'FrogSwarm',
			'GRLDAPGroup' => 'FROGSWARM',
			'GRSupervisorID' => $sa->GRID
		));
	}
}
