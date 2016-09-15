<?php

namespace App;

// use App\Game;
// use App\GameOrg;
// use App\GameUser;
// use App\User;
// use App\UserStatus;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GameUserSeeder extends Seeder {

	public function run()
	{
		DB::table('GameUser')->delete();

		$active = UserStatus::active();
		$admin = User::where('UGoonID', 'admin')->first();
		$mwo = Game::where('GAbbr', 'mwo')->first();
		$wol = GameOrg::where('GOAbbr', 'WoL')->first();

		$user = GameUser::create(array(
			'GID' => $mwo->GID,
			'UID' => $admin->UID,
			'GUUserID' => 123,
			'GUCachedName' => 'Test Kerensky'
		));

		$user->gameorgs()->attach($wol, array('USID' => $active->USID));
	}
}
