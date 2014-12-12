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
		Schema::create("User", function($t) {
			$t->increments("UID");	// User ID

			$t->integer("USID")->unsigned();	// User Status ID

			//"UIPAddress" inet,

			$t->text("ULDAPLogin")->nullable();	// LDAP login

			$t->integer("USAUserID")->nullable();		// SA user ID
			$t->timestamp("USARegDate")->nullable();	// SA reg date

			$t->timestamp("USACacheDate")->nullable();		// Date the SA info was cached
			$t->text("USACachedName")->nullable();			// Cached SA name
			$t->integer("USACachedPostCount")->nullable();	// Cached SA post count

			$t->integer("USponsorID")->unsigned()->nullable();	// Sponsor
			$t->integer("UAffiliateGroup")->nullable();			// Affiliated group
		});

		// UserStatus
		Schema::create("UserStatus", function($t) {
			$t->increments("USID");	// User Status ID

			$t->text("USStatus");	// Status text
		});

		// Note
		Schema::create("Note", function($t) {
			$t->increments("NID");	// Note ID

			$t->integer("NTID")->unsigned();	// Note Type ID
			$t->integer("UID")->unsigned();		// User ID

			$t->integer("NCreatedByUID")->unsigned();	// User who created the note

			$t->text("NNote");	// The note
		});

		// NoteType
		Schema::create("NoteType", function($t) {
			$t->increments("NTID");	// Note Type ID

			$t->text("NTName");		// Type of note
			$t->text("NTColor");	// Color of note
		});

		// Role
		Schema::create("Role", function($t) {
			$t->increments("RID");	// Role ID

			$t->text("RName");	// Role name

			$t->boolean("RPermAdd")->default(false);	// Add permission
			$t->boolean("RPermRemove")->default(false);	// Remove permission
			$t->boolean("RPermModify")->default(false);	// Modify permission
			$t->boolean("RPermAuth")->default(false);	// Auth permission
		});

		// RoleSeesNoteType
		Schema::create("RoleSeesNoteType", function($t) {
			$t->increments("RSNTID");	// Roll Sees Note Type ID

			$t->integer("RID")->unsigned();		// Role ID
			$t->integer("NTID")->unsigned();	// Note Type ID
		});

		// Group
		Schema::create("Group", function($t) {
			$t->increments("GRID");	// Group ID

			$t->integer("GROwnerID")
				->unsigned()->nullable();	// Group owner

			$t->text("GRAbbr");	// Group abbreviation
			$t->text("GRName");	// Group name
		});

		// Game
		Schema::create("Game", function($t) {
			$t->increments("GID");	// Game ID

			$t->integer("GOwnerID")
				->unsigned()->nullable();	// Game owner

			$t->text("GName");	// Game name
		});

		// GroupAdmin
		Schema::create("GroupAdmin", function($t) {
			$t->increments("GRAID");	// Group Admin ID

			$t->integer("UID")->unsigned();	// User ID
			$t->integer("RID")->unsigned();	// Role ID
		});

		// GameAdmin
		Schema::create("GameAdmin", function($t) {
			$t->increments("GAID");		// Game Admin ID

			$t->integer("UID")->unsigned();	// User ID
			$t->integer("RID")->unsigned();	// Role ID
		});

		// GroupHasNote
		Schema::create("GroupHasNote", function($t) {
			$t->increments("GRHNID");	// Group Has Note ID

			$t->integer("GRID")->unsigned();	// Group ID
			$t->integer("NID")->unsigned();		// Note ID
		});

		// GameHasNote
		Schema::create("GameHasNote", function($t) {
			$t->increments("GHNID");	// Game Has Note ID

			$t->integer("GID")->unsigned();		// Game ID
			$t->integer("NID")->unsigned();		// Note ID
		});

		// GameUser
		Schema::create("GameUser", function($t) {
			$t->increments("GUID");	// Game User ID

			$t->integer("GID")->unsigned();		// Game ID
			$t->integer("UID")->unsigned();		// User ID
			$t->integer("USID")->unsigned();	// User Status ID

			$t->integer("GUUserID");				// User's Game ID
			$t->timestamp("GURegDate")->nullable();	// User's Game Reg Date

			$t->timestamp("GUCacheDate")->nullable();		// Date the game info was cached
			$t->text("GUCachedName")->nullable();			// Cached name
			$t->integer("GUCachedPostCount")->nullable();	// Cached post count
		});

		// GameOrganization
		Schema::create("GameOrganization", function($t) {
			$t->increments("GOID");	// Game Organization ID

			$t->integer("GOOwnerID")
				->unsigned()->nullable();	// Organization owner

			$t->text("GOAbbr");	// Organization abbreviation
			$t->text("GOName");	// Organization name
		});

		// GameOrganizationHasGameUser
		Schema::create("GameOrganizationHasGameUser", function($t) {
			$t->increments("GOHGUID");	// Game Organization Has Game User ID

			$t->integer("GOID")->unsigned();	// Game Organization ID
			$t->integer("GUID")->unsigned();	// Game User ID
		});


		/** ---------- REFERENCES ---------- **/

		// User
		Schema::table("User", function($t) {
			$t->foreign("USID", "FK_User_UserStatus")
				->references("USID")->on("UserStatus")
				->onDelete("set null");

			$t->foreign("USponsorID", "FK_User_Sponsor")
				->references("UID")->on("User")
				->onDelete("restrict");
		});

		// Note
		Schema::table("Note", function($t) {
			$t->foreign("NTID", "FK_Note_NoteType")
				->references("NTID")->on("NoteType")
				->onDelete("cascade");

			$t->foreign("UID", "FK_Note_User")
				->references("UID")->on("User")
				->onDelete("cascade");

			$t->foreign("NCreatedByUID", "FK_Note_User_CreatedBy")
				->references("UID")->on("User")
				->onDelete("set null");
		});

		// RoleSeesNoteType
		Schema::table("RoleSeesNoteType", function($t) {
			$t->foreign("RID", "FK_RoleSeesNoteType_Role")
				->references("RID")->on("Role")
				->onDelete("cascade");

			$t->foreign("NTID", "FK_RoleSeesNoteType_NoteType")
				->references("NTID")->on("NoteType")
				->onDelete("cascade");
		});

		// Group
		Schema::table("Group", function($t) {
			$t->foreign("GROwnerID", "FK_Group_User")
				->references("UID")->on("User")
				->onDelete("set null");
		});

		// Game
		Schema::table("Game", function($t) {
			$t->foreign("GOwnerID", "FK_Game_User")
				->references("UID")->on("User")
				->onDelete("set null");
		});

		// GroupAdmin
		Schema::table("GroupAdmin", function($t) {
			$t->foreign("UID", "FK_GroupAdmin_User")
				->references("UID")->on("User")
				->onDelete("cascade");

			$t->foreign("RID", "FK_GroupAdmin_Role")
				->references("RID")->on("Role")
				->onDelete("cascade");
		});

		// GameAdmin
		Schema::table("GameAdmin", function($t) {
			$t->foreign("UID", "FK_GameAdmin_User")
				->references("UID")->on("User")
				->onDelete("cascade");

			$t->foreign("RID", "FK_GameAdmin_Role")
				->references("RID")->on("Role")
				->onDelete("cascade");
		});

		// GroupHasNote
		Schema::table("GroupHasNote", function($t) {
			$t->foreign("GRID", "FK_GroupHasNote_Group")
				->references("GRID")->on("Group")
				->onDelete("cascade");

			$t->foreign("NID", "FK_GroupHasNote_Note")
				->references("NID")->on("Note")
				->onDelete("cascade");
		});

		// GameHasNote
		Schema::table("GameHasNote", function($t) {
			$t->foreign("GID", "FK_GameHasNote_Game")
				->references("GID")->on("Game")
				->onDelete("cascade");

			$t->foreign("NID", "FK_GameHasNote_Note")
				->references("NID")->on("Note")
				->onDelete("cascade");
		});

		// GameUser
		Schema::table("GameUser", function($t) {
			$t->foreign("GID", "FK_GameUser_Game")
				->references("GID")->on("Game")
				->onDelete("cascade");

			$t->foreign("UID", "FK_GameUser_User")
				->references("UID")->on("User")
				->onDelete("cascade");

			$t->foreign("USID", "FK_GameUser_UserStatus")
				->references("USID")->on("UserStatus")
				->onDelete("set null");
		});

		// GameOrganization
		Schema::table("GameOrganization", function($t) {
			$t->foreign("GOOwnerID", "FK_GameOrganization_User")
				->references("UID")->on("User")
				->onDelete("set null");
		});

		// GameOrganizationHasGameUser
		Schema::table("GameOrganizationHasGameUser", function($t) {
			$t->foreign("GOID", "FK_GameOrganizationHasGameUser_GameOrganization")
				->references("GOID")->on("GameOrganization")
				->onDelete("cascade");

			$t->foreign("GUID", "FK_GameOrganizationHasGameUser_GameUser")
				->references("GUID")->on("GameUser")
				->onDelete("cascade");
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
		Schema::table("User", function($t) {
			$t->dropForeign("FK_User_UserStatus");
			$t->dropForeign("FK_User_Sponsor");
		});
		Schema::table("Note", function($t) {
			$t->dropForeign("FK_Note_NoteType");
			$t->dropForeign("FK_Note_User");
			$t->dropForeign("FK_Note_User_CreatedBy");
		});
		Schema::table("RoleSeesNoteType", function($t) {
			$t->dropForeign("FK_RoleSeesNoteType_Role");
			$t->dropForeign("FK_RoleSeesNoteType_NoteType");
		});
		Schema::table("Group", function($t) {
			$t->dropForeign("FK_Group_User");
		});
		Schema::table("Game", function($t) {
			$t->dropForeign("FK_Game_User");
		});
		Schema::table("GroupAdmin", function($t) {
			$t->dropForeign("FK_GroupAdmin_User");
			$t->dropForeign("FK_GroupAdmin_Role");
		});
		Schema::table("GameAdmin", function($t) {
			$t->dropForeign("FK_GameAdmin_User");
			$t->dropForeign("FK_GameAdmin_Role");
		});
		Schema::table("GroupHasNote", function($t) {
			$t->dropForeign("FK_GroupHasNote_Group");
			$t->dropForeign("FK_GroupHasNote_Note");
		});
		Schema::table("GameHasNote", function($t) {
			$t->dropForeign("FK_GameHasNote_Game");
			$t->dropForeign("FK_GameHasNote_Note");
		});
		Schema::table("GameUser", function($t) {
			$t->dropForeign("FK_GameUser_Game");
			$t->dropForeign("FK_GameUser_User");
			$t->dropForeign("FK_GameUser_UserStatus");
		});
		Schema::table("GameOrganization", function($t) {
			$t->dropForeign("FK_GameOrganization_User");
		});
		Schema::table("GameOrganizationHasGameUser", function($t) {
			$t->dropForeign("FK_GameOrganizationHasGameUser_GameOrganization");
			$t->dropForeign("FK_GameOrganizationHasGameUser_GameUser");
		});

		// Drop the tables.
		Schema::drop("User");
		Schema::drop("UserStatus");
		Schema::drop("Note");
		Schema::drop("NoteType");
		Schema::drop("Role");
		Schema::drop("RoleSeesNoteType");
		Schema::drop("Group");
		Schema::drop("Game");
		Schema::drop("GroupAdmin");
		Schema::drop("GameAdmin");
		Schema::drop("GroupHasNote");
		Schema::drop("GameHasNote");
		Schema::drop("GameUser");
		Schema::drop("GameOrganization");
		Schema::drop("GameOrganizationHasGameUser");
	}

}
