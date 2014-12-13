<?php

class NoteTypeSeeder extends Seeder {

	public function run()
	{
		DB::table('NoteType')->delete();

		NoteType::create(array(
			'NTName' => 'System',
			'NTColor' => '#FFFFCC'
		));
	}
}
