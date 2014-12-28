<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBaseTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// User
		Schema::create('User', function($t) {
			$t->increments('UID');	// User ID

			$t->integer('USID')->unsigned();	// User Status ID

			$t->string('UIPAddress', 24)->nullable();	// User IP Address

			$t->string('UEmail', 100)->unique();	// User E-Mail.
			$t->string('UGoonID', 100)->unique();	// User Goon ID.

			$t->integer('UGroup')->unsigned();	// Group the user belongs to
			$t->text('ULDAPLogin')->nullable();	// LDAP login

			$t->integer('USAUserID')->nullable();		// SA user ID
			$t->timestamp('USARegDate')->nullable();	// SA reg date

			$t->timestamp('USACacheDate')->nullable();		// Date the SA info was cached
			$t->text('USACachedName')->nullable();			// Cached SA name
			$t->integer('USACachedPostCount')->nullable();	// Cached SA post count

			$t->integer('USponsorID')->unsigned()->nullable();	// Sponsor
		});

		// UserStatus
		Schema::create('UserStatus', function($t) {
			$t->increments('USID');	// User Status ID

			$t->string('USCode', 4);	// Status Code
			$t->text('USStatus');		// Status text
		});

		// Note
		Schema::create('Note', function($t) {
			$t->increments('NID');	// Note ID

			$t->integer('NTID')->unsigned();	// Note Type ID
			$t->integer('UID')->unsigned();		// User ID

			$t->integer('NCreatedByUID')
				->unsigned()->nullable();	// User who created the note

			$t->timestamp('NTimestamp')
				->default(DB::raw('CURRENT_TIMESTAMP'));	// Time the note was created.

			$t->text('NNote');	// The note
		});

		// NoteType
		Schema::create('NoteType', function($t) {
			$t->increments('NTID');	// Note Type ID

			$t->string('NTCode', 4);	// Note Type Code
			$t->text('NTName');			// Type of note
			$t->text('NTColor');		// Color of note
			$t->boolean('NTSystemUseOnly')
				->default(false);		// Note type is for system use only.
		});

		// Role
		Schema::create('Role', function($t) {
			$t->increments('RID');	// Role ID

			$t->string('RCode', 4)->unique();	// Role code.
			$t->text('RName');					// Role name

			$t->boolean('RPermAdd')->default(false);	// Add permission
			$t->boolean('RPermRemove')->default(false);	// Remove permission
			$t->boolean('RPermModify')->default(false);	// Modify permission
			$t->boolean('RPermAuth')->default(false);	// Auth permission
			$t->boolean('RPermRead')->default(false);	// Read permission
		});

		// RoleSeesNoteType
		Schema::create('RoleSeesNoteType', function($t) {
			$t->increments('RSNTID');	// Roll Sees Note Type ID

			$t->integer('RID')->unsigned();		// Role ID
			$t->integer('NTID')->unsigned();	// Note Type ID
		});

		// Group
		Schema::create('Group', function($t) {
			$t->increments('GRID');	// Group ID

			$t->integer('GROwnerID')
				->unsigned()->nullable();	// Group owner

			$t->text('GRName');	// Group name

			$t->text('GRLDAPGroup')->nullable();	// LDAP group for the group.
		});

		// GroupAdmin
		Schema::create('GroupAdmin', function($t) {
			$t->increments('GRAID');	// Group Admin ID

			$t->integer('GRID')->unsigned();	// Group ID
			$t->integer('UID')->unsigned();		// User ID
			$t->integer('RID')->unsigned();		// Role ID
		});

		// Game
		Schema::create('Game', function($t) {
			$t->increments('GID');	// Game ID

			$t->text('GAbbr');	// Game abbreviation
			$t->text('GName');	// Game name
			$t->text('GEditProfileURL');		// Game edit profile URL
			$t->text('GProfileURL')->nullable;	// Game profile URL
			$t->boolean('GRequireValidation')->default(true);	// Game requires profile validation.

			$t->text('GLDAPGroup')->nullable();	// LDAP group for the game.
		});

		// GameOrg
		Schema::create('GameOrg', function($t) {
			$t->increments('GOID');	// Game Org ID

			$t->integer('GOOwnerID')
				->unsigned()->nullable();	// Org owner

			$t->text('GOAbbr');	// Org abbreviation
			$t->text('GOName');	// Org name

			$t->text('GOLDAPGroup')->nullable();	// LDAP group for the game org.
		});

		// GameUser
		Schema::create('GameUser', function($t) {
			$t->increments('GUID');	// Game User ID

			$t->integer('GID')->unsigned();		// Game ID
			$t->integer('UID')->unsigned();		// User ID

			$t->integer('GUUserID')->nullable();	// User's Game ID
			$t->timestamp('GURegDate')->nullable();	// User's Game Reg Date

			$t->timestamp('GUCacheDate')->nullable();		// Date the game info was cached
			$t->text('GUCachedName')->nullable();			// Cached name
			$t->integer('GUCachedPostCount')->nullable();	// Cached post count
		});

		// GameHasGameOrg
		Schema::create('GameHasGameOrg', function($t) {
			$t->increments('GHGOID');	// Game Has Game Org ID

			$t->integer('GID')->unsigned();		// Game ID
			$t->integer('GOID')->unsigned();	// Game Org ID
		});

		// GameOrgHasGameUser
		Schema::create('GameOrgHasGameUser', function($t) {
			$t->increments('GOHGUID');	// Game Org Has Game User ID

			$t->integer('USID')->unsigned();	// User Status ID

			$t->integer('GOID')->unsigned();	// Game Org ID
			$t->integer('GUID')->unsigned();	// Game User ID
		});

		// GameOrgAdmin
		Schema::create('GameOrgAdmin', function($t) {
			$t->increments('GOAID');	// Game Admin ID

			$t->integer('GOID')->unsigned();	// Game Org ID
			$t->integer('UID')->unsigned();		// User ID
			$t->integer('RID')->unsigned();		// Role ID
		});

		// GroupHasNote
		Schema::create('GroupHasNote', function($t) {
			$t->increments('GRHNID');	// Group Has Note ID

			$t->integer('GRID')->unsigned();	// Group ID
			$t->integer('NID')->unsigned();		// Note ID
		});

		// GameOrgHasNote
		Schema::create('GameOrgHasNote', function($t) {
			$t->increments('GOHNID');	// Game Org Has Note ID

			$t->integer('GOID')->unsigned();	// Game Org ID
			$t->integer('NID')->unsigned();		// Note ID
		});


		/** ---------- REFERENCES ---------- **/

		// User
		Schema::table('User', function($t) {
			$t->foreign('USID', 'FK_User_UserStatus')
				->references('USID')->on('UserStatus')
				->onDelete('restrict');

			$t->foreign('USponsorID', 'FK_User_Sponsor')
				->references('UID')->on('User')
				->onDelete('restrict');

			$t->foreign('UGroup', 'FK_User_Group')
				->references('GRID')->on('Group')
				->onDelete('restrict');
		});

		// Note
		Schema::table('Note', function($t) {
			$t->foreign('NTID', 'FK_Note_NoteType')
				->references('NTID')->on('NoteType')
				->onDelete('cascade');

			$t->foreign('UID', 'FK_Note_User')
				->references('UID')->on('User')
				->onDelete('cascade');

			$t->foreign('NCreatedByUID', 'FK_Note_User_CreatedBy')
				->references('UID')->on('User')
				->onDelete('set null');
		});

		// RoleSeesNoteType
		Schema::table('RoleSeesNoteType', function($t) {
			$t->foreign('RID', 'FK_RoleSeesNoteType_Role')
				->references('RID')->on('Role')
				->onDelete('cascade');

			$t->foreign('NTID', 'FK_RoleSeesNoteType_NoteType')
				->references('NTID')->on('NoteType')
				->onDelete('cascade');
		});

		// Group
		Schema::table('Group', function($t) {
			$t->foreign('GROwnerID', 'FK_Group_User')
				->references('UID')->on('User')
				->onDelete('set null');
		});

		// GroupAdmin
		Schema::table('GroupAdmin', function($t) {
			$t->foreign('GRID', 'FK_GroupAdmin_Group')
				->references('GRID')->on('Group')
				->onDelete('cascade');

			$t->foreign('UID', 'FK_GroupAdmin_User')
				->references('UID')->on('User')
				->onDelete('cascade');

			$t->foreign('RID', 'FK_GroupAdmin_Role')
				->references('RID')->on('Role')
				->onDelete('cascade');
		});

		// GameOrg
		Schema::table('GameOrg', function($t) {
			$t->foreign('GOOwnerID', 'FK_GameOrg_User')
				->references('UID')->on('User')
				->onDelete('set null');
		});

		// GameUser
		Schema::table('GameUser', function($t) {
			$t->foreign('GID', 'FK_GameUser_Game')
				->references('GID')->on('Game')
				->onDelete('cascade');

			$t->foreign('UID', 'FK_GameUser_User')
				->references('UID')->on('User')
				->onDelete('cascade');
		});

		// GameHasGameOrg
		Schema::table('GameHasGameOrg', function($t) {
			$t->foreign('GID', 'FK_GameHasGameOrg_Game')
				->references('GID')->on('Game')
				->onDelete('cascade');

			$t->foreign('GOID', 'FK_GameHasGameOrg_GameOrg')
				->references('GOID')->on('GameOrg')
				->onDelete('cascade');
		});

		// GameOrgHasGameUser
		Schema::table('GameOrgHasGameUser', function($t) {
			$t->foreign('USID', 'FK_GameOrgHasGameUser_UserStatus')
				->references('USID')->on('UserStatus')
				->onDelete('restrict');

			$t->foreign('GOID', 'FK_GameOrgHasGameUser_GameOrg')
				->references('GOID')->on('GameOrg')
				->onDelete('cascade');

			$t->foreign('GUID', 'FK_GameOrgHasGameUser_GameUser')
				->references('GUID')->on('GameUser')
				->onDelete('cascade');
		});

		// GameOrgAdmin
		Schema::table('GameOrgAdmin', function($t) {
			$t->foreign('GOID', 'FK_GameOrgAdmin_GameOrg')
				->references('GOID')->on('GameOrg')
				->onDelete('cascade');

			$t->foreign('UID', 'FK_GameOrgAdmin_User')
				->references('UID')->on('User')
				->onDelete('cascade');

			$t->foreign('RID', 'FK_GameOrgAdmin_Role')
				->references('RID')->on('Role')
				->onDelete('cascade');
		});

		// GroupHasNote
		Schema::table('GroupHasNote', function($t) {
			$t->foreign('GRID', 'FK_GroupHasNote_Group')
				->references('GRID')->on('Group')
				->onDelete('cascade');

			$t->foreign('NID', 'FK_GroupHasNote_Note')
				->references('NID')->on('Note')
				->onDelete('cascade');
		});

		// GameOrgHasNote
		Schema::table('GameOrgHasNote', function($t) {
			$t->foreign('GOID', 'FK_GameOrgHasNote_GameOrg')
				->references('GOID')->on('GameOrg')
				->onDelete('cascade');

			$t->foreign('NID', 'FK_GameOrgHasNote_Note')
				->references('NID')->on('Note')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Remove the references.
		Schema::table('User', function($t) {
			$t->dropForeign('FK_User_UserStatus');
			$t->dropForeign('FK_User_Sponsor');
		});
		Schema::table('Note', function($t) {
			$t->dropForeign('FK_Note_NoteType');
			$t->dropForeign('FK_Note_User');
			$t->dropForeign('FK_Note_User_CreatedBy');
		});
		Schema::table('RoleSeesNoteType', function($t) {
			$t->dropForeign('FK_RoleSeesNoteType_Role');
			$t->dropForeign('FK_RoleSeesNoteType_NoteType');
		});
		Schema::table('Group', function($t) {
			$t->dropForeign('FK_Group_User');
		});
		Schema::table('GroupAdmin', function($t) {
			$t->dropForeign('FK_GroupAdmin_Group');
			$t->dropForeign('FK_GroupAdmin_User');
			$t->dropForeign('FK_GroupAdmin_Role');
		});
		Schema::table('GameOrg', function($t) {
			$t->dropForeign('FK_GameOrg_User');
		});
		Schema::table('GameUser', function($t) {
			$t->dropForeign('FK_GameUser_Game');
			$t->dropForeign('FK_GameUser_User');
		});
		Schema::table('GameHasGameOrg', function($t) {
			$t->dropForeign('FK_GameHasGameOrg_Game');
			$t->dropForeign('FK_GameHasGameOrg_GameOrg');
		});
		Schema::table('GameOrgHasGameUser', function($t) {
			$t->dropForeign('FK_GameOrgHasGameUser_UserStatus');
			$t->dropForeign('FK_GameOrgHasGameUser_GameOrg');
			$t->dropForeign('FK_GameOrgHasGameUser_GameUser');
		});
		Schema::table('GameOrgAdmin', function($t) {
			$t->dropForeign('FK_GameOrgAdmin_GameOrg');
			$t->dropForeign('FK_GameOrgAdmin_User');
			$t->dropForeign('FK_GameOrgAdmin_Role');
		});
		Schema::table('GroupHasNote', function($t) {
			$t->dropForeign('FK_GroupHasNote_Group');
			$t->dropForeign('FK_GroupHasNote_Note');
		});
		Schema::table('GameOrgHasNote', function($t) {
			$t->dropForeign('FK_GameOrgHasNote_GameOrg');
			$t->dropForeign('FK_GameOrgHasNote_Note');
		});

		// Drop the tables.
		Schema::drop('User');
		Schema::drop('UserStatus');
		Schema::drop('Note');
		Schema::drop('NoteType');
		Schema::drop('Role');
		Schema::drop('RoleSeesNoteType');
		Schema::drop('Group');
		Schema::drop('GroupAdmin');
		Schema::drop('Game');
		Schema::drop('GameOrg');
		Schema::drop('GameUser');
		Schema::drop('GameHasGameOrg');
		Schema::drop('GameOrgHasGameUser');
		Schema::drop('GameOrgAdmin');
		Schema::drop('GroupHasNote');
		Schema::drop('GameOrgHasNote');
	}

}
