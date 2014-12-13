<?php

class GameUserSeeder extends Seeder {

	public function run()
	{
		DB::table('GameUser')->delete();

		$active = UserStatus::active()->first();
		$admin = User::where('UGoonID', Config::get('goonauth.adminAccount'))->first();
		$mwo = Game::where('GName', 'MechWarrior Online')->first();
		$wol = GameOrganization::where('GOAbbr', 'WoL')->first();

		$user = GameUser::create(array(
			'GID' => $mwo->GID,
			'UID' => $admin->UID,
			'USID' => $active->USID,
			'GUUserID' => 123
		));

		$user->gameorganizations()->attach($wol);
	}
}
