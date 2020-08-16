<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExtensionModules extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Modify Game table.
		Schema::table('Game', function($t) {
			$t->text('GModulePHP')->nullable();
			$t->dropColumn('GRequireValidation');
		});

		// Modify GameOrg table.
		Schema::table('GameOrg', function($t) {
			$t->text('GOModulePHP')->nullable();
		});

		// Insert defaults.
		DB::transaction(function ()
		{
			DB::table('Game')
				->where('GAbbr', 'sc')
				->update(array('GModulePHP' => 'StarCitizenGame'));

			DB::table('Game')
				->where('GAbbr', 'mwo')
				->update(array('GModulePHP' => 'MWOGame'));

			DB::table('Game')
				->where('GAbbr', 'ee')
				->update(array('GModulePHP' => 'EveEchoesGame'));

			DB::table('GameOrg')
				->where('GOAbbr', 'GOON')
				->update(array('GOModulePHP' => 'GOONOrg'));

			DB::table('GameOrg')
				->where('GOAbbr', 'WAFFE')
				->update(array('GOModulePHP' => 'WAFFEOrg'));

			DB::table('GameOrg')
				->where('GOAbbr', 'KRMA')
				->update(array('GOModulePHP' => 'KRMAOrg'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Revert Game table.
		Schema::table('Game', function($t) {
			$t->dropColumn('GModulePHP');
			$t->boolean('GRequireValidation')->default(true);
		});

		// Revert GameOrg table.
		Schema::table('GameOrg', function($t) {
			$t->dropColumn('GOModulePHP');
		});

		// Fix MWO validation.
		DB::transaction(function ()
		{
			DB::table('Game')
				->where('GAbbr', 'mwo')
				->update(array('GRequireValidation' => false));
		});
	}

}
