<?php

class RoleSeeder extends Seeder {

	public function run()
	{
		DB::table('Role')->delete();

		$admin = Role::create(array(
			'RCode' => 'ADM',
			'RName' => 'Admin',
			'RPermAdd' => true,
			'RPermRemove' => true,
			'RPermModify' => true,
			'RPermAuth' => true,
			'RPermRead' => true
		));

		$notes = Role::create(array(
			'RCode' => 'NOTE',
			'RName' => 'Read General Notes',
			'RPermAdd' => false,
			'RPermRemove' => false,
			'RPermModify' => false,
			'RPermAuth' => false,
			'RPermRead' => true
		));

		$director = Role::create(array(
			'RCode' => 'DIR',
			'RName' => 'Director',
			'RPermAdd' => true,
			'RPermRemove' => true,
			'RPermModify' => true,
			'RPermAuth' => true,
			'RPermRead' => true
		));

		$auth = Role::create(array(
			'RCode' => 'AUTH',
			'RName' => 'Auth',
			'RPermAdd' => false,
			'RPermRemove' => false,
			'RPermModify' => false,
			'RPermAuth' => true,
			'RPermRead' => true
		));

		$intel = Role::create(array(
			'RCode' => 'INT',
			'RName' => 'Intel',
			'RPermAdd' => false,
			'RPermRemove' => false,
			'RPermModify' => false,
			'RPermAuth' => false,
			'RPermRead' => true
		));

		$sys = NoteType::where('NTCode', 'SYS')->first()->NTID;
		$stat = NoteType::where('NTCode', 'STAT')->first()->NTID;
		$lega = NoteType::where('NTCode', 'LEGA')->first()->NTID;
		$gen = NoteType::where('NTCode', 'GEN')->first()->NTID;
		$int = NoteType::where('NTCode', 'INT')->first()->NTID;

		// Admin role gets to see/create all notes.
		foreach (NoteType::get() as $note)
			$admin->notetypes()->attach($note->NTID);

		// Read General Notes can see SYS, STAT, LEGA, and GEN notes.
		$notes->notetypes()->attach(array($sys, $stat, $lega, $gen));

		// Director role doesn't see any special notes.

		// Auth role doesn't see any special notes.

		// Intel role can see INT notes.
		$auth->notetypes()->attach($int);
	}
}
