<?php

namespace App;

// use App\GameOrg;
// use App\Group;
// use App\Note;
// use App\NoteType;
// use App\Role;
// use App\User;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class NoteSeeder extends Seeder {

	public function run()
	{
		DB::table('Note')->delete();

		// Administration
		$admin = User::where('UGoonID', config('goonauth.admin'))->first();
		$roll = Role::where('RName', 'Admin')->first();

		// Groups
		$sa = Group::where('GRCode', 'SA')->first();
		$sa = Group::where('GRCode', 'SA')->first();

		// Organisations
		$goon = GameOrg::where('GOAbbr', 'GOON')->first();
		$waffe = GameOrg::where('GOAbbr', 'WAFFE')->first();
		$krma = GameOrg::where('GOAbbr', 'KRMA')->first();

		$system = NoteType::where('NTCode', 'SYS')->first();
		$status = NoteType::where('NTCode', 'STAT')->first();

		$n_join_sa = Note::create(array(
			'NTID' => $status->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Authentication',
			'NMessage' => 'User accepted into group Something Awful.'
		));

		// GoonFLeet (SA)
		$n_owner_goon = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Ownership Change',
			'NMessage' => 'User has taken ownership of GoonFleet organization.'
		));

		// GoonWaffe
		$n_owner_waffe = Note::create(array(
			'NTID' => $system->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Ownership Change',
			'NMessage' => 'User has taken ownership of GoonWaffe organization.'
		));

		// KarmaFleet
		$n_join_krma = Note::create(array(
			'NTID' => $status->NTID,
			'UID' => $admin->UID,
			'NCreatedByUID' => null,
			'NSubject' => 'Authentication',
			'NMessage' => 'User accepted into organization Word of Lowtax.'
		));

		$sa->notes()->attach($n_join_sa);
		$goon->notes()->attach($n_owner_goon);
		$waffe->notes()->attach($n_owner_waffe);
		$krma->notes()->attach($n_join_krma);
	}
}
