<?php

class UserSeeder extends Seeder {

	public function run()
	{
		DB::table('User')->delete();

		$active = UserStatus::where('USCode', '=', 'ACTI')->first();

		User::create(array(
			'USID' => $active->USID,
			'UEmail' => 'example@example.com',
			'UGoonID' => 'Nalin'
		));
	}
}
