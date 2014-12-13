<?php

class GameSeeder extends Seeder {

	public function run()
	{
		DB::table('Game')->delete();

		$admin = User::where('UGoonID', Config::get('goonauth.adminAccount'))->first();
		$roll = Role::where('RName', 'Admin')->first();

		$sc = Game::create(array(
			'GOwnerID' => $admin->UID,
			'GAbbr' => 'sc',
			'GName' => 'Star Citizen',
			'GProfileURL' => 'https://robertsspaceindustries.com/account/profile'
		));

		$mwo = Game::create(array(
			'GOwnerID' => $admin->UID,
			'GAbbr' => 'mwo',
			'GName' => 'MechWarrior Online',
			'GProfileURL' => 'http://mwomercs.com/forums/index.php?app=core&module=usercp&tab=core'
		));

		$fljk = GameOrganization::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'FLJK',
			'GOName' => 'Goonrathi'
		));

		$wol = GameOrganization::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'WoL',
			'GOName' => 'Word of Lowtax'
		));

		$sc->organizations()->attach($sc->GID);
		$mwo->organizations()->attach($mwo->GID);

		$admin->gameroles()->attach($roll, array('GID' => $sc->GID));
		$admin->gameroles()->attach($roll, array('GID' => $mwo->GID));
	}
}
