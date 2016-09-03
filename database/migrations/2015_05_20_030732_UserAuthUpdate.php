<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAuthUpdate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Add the remember token to the user table.
		Schema::table('User', function($t) {
			$t->string('URememberToken', 100)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Revert changes.
		Schema::table('User', function($t) {
			$t->dropColumn('URememberToken');
		});
	}

}
