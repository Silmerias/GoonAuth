<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsoringTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Modify sponsor table.
		Schema::table('Sponsor', function($t) {
			// Add our new columns.
			$t->string('SCode', 10)->unique();
			$t->integer('SSponsoredID')->nullable();
		});
		
		Schema::table('Sponsor', function($t) {
			// Add new constraints.
			$t->foreign('SSponsoredID', 'FK_Sponsor_User_Sponsored')
				->references('UID')->on('User')
				->onDelete('set null');
		});

		Schema::table('Sponsor', function($t) {
			// Break FK contraints on old columns.
			$t->dropForeign('FK_Sponsor_Group');
			$t->dropForeign('FK_Sponsor_GameOrg');
			$t->dropForeign('FK_Sponsor_User_Sponsor');
		});

		Schema::table('Sponsor', function($t) {
			// Drop our old columns.
			$t->dropColumn('GRID');
			$t->dropColumn('GOID');
			$t->dropColumn('SSponsorID');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('Sponsor', function($t) {
			// Drop our new columns.
			$t->dropColumn('SCode');
			$t->dropColumn('SSponsoredID');
		});

		Schema::table('Sponsor', function($t) {
			// Add back the old columns.
			$t->integer('SSponsorID')->unsigned();	// Sponsoring user.
			$t->integer('GRID')
				->unsigned()->nullable();	// Group sponsorship.
			$t->integer('GOID')
				->unsigned()->nullable();	// Game Org sponsorship.
		});

		Schema::table('Sponsor', function($t) {
			// Add back the old FK constraints.
			$t->foreign('SSponsorID', 'FK_Sponsor_User_Sponsor')
				->references('UID')->on('User')
				->onDelete('cascade');
		});

		Schema::table('Sponsor', function($t) {
			$t->foreign('GRID', 'FK_Sponsor_Group')
				->references('GRID')->on('Group')
				->onDelete('cascade');

			$t->foreign('GOID', 'FK_Sponsor_GameOrg')
				->references('GOID')->on('GameOrg')
				->onDelete('cascade');
		});
	}

}
