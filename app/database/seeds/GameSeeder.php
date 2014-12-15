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
			'GEditProfileURL' => 'https://robertsspaceindustries.com/account/profile',
			'GProfileURL' => 'https://robertsspaceindustries.com/citizens/%s'
		));

		$mwo = Game::create(array(
			'GOwnerID' => $admin->UID,
			'GAbbr' => 'mwo',
			'GName' => 'MechWarrior Online',
			'GEditProfileURL' => 'http://mwomercs.com/forums/index.php?app=core&module=usercp&tab=core',
			'GProfileURL' => 'http://mwomercs.com/forums/user/%d-%s'
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
