<?php

namespace App;

// use App\NoteType;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoteTypeSeeder extends Seeder {

	public function run()
	{
		DB::table('NoteType')->delete();

		// Special system notes.
		NoteType::create(array(
			'NTCode' => 'SYS',
			'NTName' => 'System',
			'NTColor' => '#FFFFCC',
			'NTSystemUseOnly' => true
		));

		// Status change notes.
		NoteType::create(array(
			'NTCode' => 'STAT',
			'NTName' => 'Status Change',
			'NTColor' => '#CCFFCC',
			'NTSystemUseOnly' => true
		));

		// Legacy notes.
		NoteType::create(array(
			'NTCode' => 'LEGA',
			'NTName' => 'Legacy',
			'NTColor' => '#CCCCCC',
			'NTSystemUseOnly' => true
		));

		NoteType::create(array(
			'NTCode' => 'GEN',
			'NTName' => 'General',
			'NTColor' => '#FFCCFF'
		));

		NoteType::create(array(
			'NTCode' => 'INT',
			'NTName' => 'Intelligence',
			'NTColor' => '#CCFFFF'
		));
	}
}
