<?php

class GameUserSeeder extends Seeder {

	public function run()
	{
		DB::table('GameUser')->delete();

		$active = UserStatus::active()->first();
		$admin = User::where('UGoonID', Config::get('goonauth.adminAccount'))->first();
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
