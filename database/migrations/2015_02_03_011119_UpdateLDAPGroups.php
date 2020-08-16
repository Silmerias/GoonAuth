<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLDAPGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Insert LDAP groups for default Game entries.
		DB::transaction(function ()
		{
			DB::table('Game')
				->where('GAbbr', 'ee')
				->update(array('GLDAPGroup' => 'EE'));

			DB::table('Game')
				->where('GAbbr', 'sc')
				->update(array('GLDAPGroup' => 'SC'));

			DB::table('Game')
				->where('GAbbr', 'mwo')
				->update(array('GLDAPGroup' => 'MWO'));
		});

		// Add unique constraints to the abbreviations.
		/*
		Schema::table('Game', function($t) {
			$t->unique('GAbbr');
		});
		Schema::table('GameOrg', function($t) {
			$t->unique('GOAbbr');
		});
		*/
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Remove the LDAP groups.
		DB::transaction(function ()
		{
			DB::table('Game')
				->where('GAbbr', 'ee')
				->update(array('GLDAPGroup' => null));

			DB::table('Game')
				->where('GAbbr', 'sc')
				->update(array('GLDAPGroup' => null));

			DB::table('Game')
				->where('GAbbr', 'mwo')
				->update(array('GLDAPGroup' => null));
		});

		// Remove unique constraints.
		/*
		Schema::table('Game', function($t) {
			$t->dropUnique('Game_GAbbr_unique');
		});
		Schema::table('GameOrg', function($t) {
			$t->dropUnique('GameOrg_GOAbbr_unique');
		});
		*/
	}
}
