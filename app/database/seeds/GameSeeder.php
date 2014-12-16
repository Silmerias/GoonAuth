<?php

class GameSeeder extends Seeder {

	public function run()
	{
		DB::table('Game')->delete();

		$admin = User::where('UGoonID', Config::get('goonauth.adminAccount'))->first();
		$roll = Role::where('RName', 'Admin')->first();

		$sc = Game::create(array(
			'GAbbr' => 'sc',
			'GName' => 'Star Citizen',
			'GLDAPGroup' => null,	// SC
			'GEditProfileURL' => 'https://robertsspaceindustries.com/account/profile',
			'GProfileURL' => 'https://robertsspaceindustries.com/citizens/%s'
		));

		$mwo = Game::create(array(
			'GAbbr' => 'mwo',
			'GName' => 'MechWarrior Online',
			'GLDAPGroup' => null,	// MWO
			'GEditProfileURL' => 'http://mwomercs.com/forums/index.php?app=core&module=usercp&tab=core',
			'GProfileURL' => 'http://mwomercs.com/forums/user/%d-%s'
		));

		$fljk = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'FLJK',
			'GOName' => 'Goonrathi',
			'GOLDAPGroup' => 'FLJK'
		));

		$wol = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'WoL',
			'GOName' => 'Word of Lowtax',
			'GOLDAPGroup' => 'WOL'
		));

		$sc->orgs()->attach($sc->GID);
		$mwo->orgs()->attach($mwo->GID);

		$admin->gameorgroles()->attach($roll, array('GOID' => $sc->GID));
		$admin->gameorgroles()->attach($roll, array('GOID' => $mwo->GID));
	}
}
