<?php

class NoteSeeder extends Seeder {

	public function run()
	{
		DB::table('Note')->delete();

		$roll = Role::where('RName', 'Admin')->first();
		$system = NoteType::where('NTName', 'System')->first();
		$sa = Group::first();
		$sc = Game::where('GName', 'Star Citizen')->first();
		$admin = User::where('UGoonID', Config::get('goonauth.adminAccount'))->first();

		$n_created = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NNote' => 'User created.'
		));

		$n_owner = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NNote' => 'User has taken ownership.'
		));

		$sa->notes()->attach($n_created);
		$sc->notes()->attach($n_owner);
	}
}
