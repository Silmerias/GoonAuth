<?php

class RoleSeeder extends Seeder {

	public function run()
	{
		DB::table('Role')->delete();

		$role = Role::create(array(
			'RName' => 'Admin',
			'RPermAdd' => true,
			'RPermRemove' => true,
			'RPermModify' => true,
			'RPermAuth' => true,
			'RPermRead' => true
		));

		// Admin role gets to see/create all notes.
		foreach (NoteType::get() as $note)
			$role->notetypes()->attach($note);
	}
}
