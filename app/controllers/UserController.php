<?php

class UserController extends BaseController
{

	public function showHome()
	{
		$auth = Session::get('auth');

		$include = array('auth' => $auth);
		return View::make('user.home', $include);
	}

	public function doLogin()
	{
		$valid = $this->LDAPPasswordCheck(Input::get('goonid'), Input::get('password'));
		if ($valid === false)
		{
			Session::flash('error', 'Invalid username/password.');
			return View::make('user.login');
		}

		$user = User::where('UGoonID', Input::get('goonid'))->first();
		if (!isset($user))
		{
			Session::flash('error', 'This user is not in our system.');
			return View::make('user.login');
		}

		Session::put('authenticated', true);
		Session::put('userid', $user->UID);
		Session::put('displayUsername', Input::get('goonid'));

		return Redirect::to('/');
	}

	public function showUser($id)
	{
		$auth = Session::get('auth');
		$user = User::find($id);

		// Get all groups the user has permissions in.
		$groups = DB::table('Group')
			->join('GroupAdmin', 'Group.GRID', '=', 'GroupAdmin.GRID')
			->join('Role', 'GroupAdmin.RID', '=', 'Role.RID')
			->where('GroupAdmin.UID', '=', $auth->UID)
			->where('Role.RPermRead', '=', true)
			->get();

		// Get all the orgs the user has permissions in.
		$orgs = DB::table('GameOrg')
			->join('GameOrgAdmin', 'GameOrg.GOID', '=', 'GameOrgAdmin.GOID')
			->join('Role', 'GameOrgAdmin.RID', '=', 'Role.RID')
			->where('GameOrgAdmin.UID', '=', $auth->UID)
			->where('Role.RPermRead', '=', true)
			->get();

		// Assemble the list of ids.
		$grid = array();
		$goid = array();
		foreach ($groups as $e)
			$gdrid[] = $e->GRID;
		foreach ($orgs as $e)
			$goid[] = $e->GOID;

		$notes = null;
		if (!empty($grid) || !empty($goid))
		{
			// Find all valid notes.
			$notes = DB::table('Note')
				->join('NoteType', 'Note.NTID', '=', 'NoteType.NTID')
				->leftJoin('User', 'Note.UID', '=', 'User.UID')
				->leftJoin('User AS Created', 'Note.NCreatedByUID', '=', 'Created.UID')
				->select('Note.NID as NID', 'Note.NNote as NNote', 'Note.NTimestamp as NTimestamp')
				->addSelect('NoteType.NTColor as NTColor', 'NoteType.NTName as NTName')
				->addSelect('User.UGoonID as UGoonID', 'Created.UGoonID as CreatedGoonID', 'Created.UID as CreatedUID');

			if (!empty($grid))
				$notes->leftJoin('GroupHasNote', 'Note.NID', '=', 'GroupHasNote.NID');
			if (!empty($goid))
				$notes->leftJoin('GameOrgHasNote', 'Note.NID', '=', 'GameOrgHasNote.NID');

			$notes->where('User.UID', $user->UID);
			$notes->where(function($q) use($grid, $goid) {
				if (!empty($grid))
					$q->orWhereIn('GroupHasNote.GRID', $grid);
				if (!empty($goid))
					$q->orWhereIn('GameOrgHasNote.GOID', $goid);
			});

			$notes = $notes->get();
		}

		$include = array('auth' => $auth, 'user' => $user, 'notes' => $notes);
		return View::make('user.stats', $include);
	}


	private function LDAPExecute( $func )
	{
		if (Config::get('goonauth.disableLDAP'))
			return;

		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldap] Could not connect to $ldaphost");
			return;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = Config::get('goonauth.ldapUser');
		$ldappass = Config::get('goonauth.ldapPassword');

		// If nothing was entered, use NULL so we do an anonymous bind.
		if (strlen($ldapuser) === 0)
			$ldapuser = NULL;
		else $ldapuser = "cn=" . $ldapuser . "," . Config::get('goonauth.ldapDN');
		if (strlen($ldappass) === 0)
			$ldappass = NULL;

		// Attempt to bind now.
		if (ldap_bind($ldap, $ldapuser, $ldappass))
		{
			// Execute our function.
			$func($ldap);
		}
		else
		{
			error_log("[ldap] Failed to bind to $ldaphost : $ldapport with user $ldapuser");
		}

		ldap_close($ldap);
	}

	private function LDAPPasswordCheck($username, $password)
	{
		if (Config::get('goonauth.disableLDAP'))
			return true;

		if (!isset($username) || !isset($password) || strlen($password) == 0)
			return false;

		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldap] Could not connect to $ldaphost");
			return false;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = "cn=" . $username . "," . Config::get('goonauth.ldapDN');
		$ldappass = $password;

		// Attempt to bind now.
		$ret = false;
		try
		{
			if (ldap_bind($ldap, $ldapuser, $ldappass))
				$ret = true;
		}
		catch (Exception $e) {}

		ldap_close($ldap);
		return $ret;
	}
}
