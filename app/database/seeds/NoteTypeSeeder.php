<?php

class NoteTypeSeeder extends Seeder {

	public function run()
	{
		DB::table('NoteType')->delete();

		NoteType::create(array(
			'NTCode' => 'SYS',
			'NTName' => 'System',
			'NTColor' => '#FFFFCC'
		));

		NoteType::create(array(
			'NTCode' => 'REG',
			'NTName' => 'Registration',
			'NTColor' => '#CCFFCC'
		));

		NoteType::create(array(
			'NTCode' => 'AUTH',
			'NTName' => 'Authorization',
			'NTColor' => '#CCFFFF'
		));
	}
}
