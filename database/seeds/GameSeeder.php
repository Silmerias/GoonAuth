<?php

namespace App;

/*
use App\Game;
use App\GameOrg;
use App\Role;
use App\User;
*/

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GameSeeder extends Seeder {

	public function run()
	{
		DB::table('Game')->delete();

		$admin = User::where('UGoonID', 'admin')->first();
		$roll = Role::where('RName', 'Admin')->first();

		$sc = Game::create(array(
			'GAbbr' => 'sc',
			'GName' => 'Star Citizen',
			'GLDAPGroup' => 'GameStarCitizen',	// SC
			'GEditProfileURL' => 'https://robertsspaceindustries.com/account/profile',
			'GProfileURL' => 'https://robertsspaceindustries.com/citizens/%s',
			'GModulePHP' => 'StarCitizen'
		));

		$mwo = Game::create(array(
			'GAbbr' => 'mwo',
			'GName' => 'MechWarrior Online',
			'GLDAPGroup' => 'GameMechWarriorOnline',	// MWO
			'GEditProfileURL' => 'http://mwomercs.com/forums/index.php?app=core&module=usercp&tab=core',
			'GProfileURL' => 'http://mwomercs.com/forums/user/%d-%s',
			'GModulePHP' => 'MWO'
		));

		$fljk = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'FLJK',
			'GOName' => 'Goonrathi',
			'GOLDAPGroup' => 'OrgFLJK',
			'GOModulePHP' => 'FLJK'
		));

		$wol = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'WoL',
			'GOName' => 'Word of Lowtax',
			'GOLDAPGroup' => 'OrgWOL'
		));

		$sc->orgs()->attach($fljk->GOID);
		$mwo->orgs()->attach($wol->GOID);

		$admin->gameorgroles()->attach($roll, array('GOID' => $sc->GID));
		$admin->gameorgroles()->attach($roll, array('GOID' => $mwo->GID));
	}
}
