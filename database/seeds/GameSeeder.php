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

		$admin = User::where('UGoonID', config('goonauth.admin'))->first();
		$roll = Role::where('RName', 'Admin')->first();

		// StarCitizen
		$sc = Game::create(array(
			'GAbbr' => 'sc',
			'GName' => 'Star Citizen',
			'GLDAPGroup' => 'GameStarCitizen',	// SC
			'GEditProfileURL' => 'https://robertsspaceindustries.com/account/profile',
			'GProfileURL' => 'https://robertsspaceindustries.com/citizens/%s',
			'GModulePHP' => 'StarCitizen'
		));

		// MechWarrior
		$mwo = Game::create(array(
			'GAbbr' => 'mwo',
			'GName' => 'MechWarrior Online',
			'GLDAPGroup' => 'GameMechWarriorOnline',	// MWO
			'GEditProfileURL' => 'http://mwomercs.com/forums/index.php?app=core&module=usercp&tab=core',
			'GProfileURL' => 'http://mwomercs.com/forums/user/%d-%s',
			'GModulePHP' => 'MWO'
		));

		// Eve Echoes
		$ee = Game::create(array(
			'GAbbr' => 'ee',
			'GName' => 'Eve Echoes',
			'GLDAPGroup' => 'GameEveEchoes',
			'GEditProfileURL' => '',
			'GProfileURL' => '',
			'GModulePHP' => 'EveEchoes'
		));

		// GoonFleet (SA)
		$goon = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'GOON',
			'GOName' => 'GoonFleet',
			'GOLDAPGroup' => 'OrgGOON',
			'GOModulePHP' => 'GOON'
		));

		// GoonFleet
		$waffe = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'WAFFE',
			'GOName' => 'GoonWaffe',
			'GOLDAPGroup' => 'OrgWAFFE',
			'GOModulePHP' => 'WAFFE'
		));

		// GoonFleet
		$krma = GameOrg::create(array(
			'GOOwnerID' => $admin->UID,
			'GOAbbr' => 'KRMA',
			'GOName' => 'KarmaFleet',
			'GOLDAPGroup' => 'OrgKRMA',
			'GOModulePHP' => 'KRMA'
		));

		$ee->orgs()->attach($goon->GOID);
		$ee->orgs()->attach($waffe->GOID);
		$ee->orgs()->attach($krma->GOID);

		$admin->gameorgroles()->attach($roll, array('GOID' => $ee->GID));
	}
}
