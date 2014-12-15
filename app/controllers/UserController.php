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
			return Redirect::back()->with('error', 'Invalid username/password.');

		$user = User::where('UGoonID', Input::get('goonid'))->first();
		if (!isset($user))
			return Redirect::back()->with('error', 'This user is not in our system.');

		Session::put('authenticated', true);
		Session::put('userid', $user->UID);
		Session::put('displayUsername', Input::get('goonid'));

		return Redirect::to('/');
	}

	public function showUser($id)
	{
		$auth = Session::get('auth');
		$user = User::find($id);

		$include = array('auth' => $auth, 'user' => $user);
		return View::make('user.stats', $include);
	}


	private function LDAPExecute( $func )
	{
		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldapsync] Could not connect to $ldaphost");
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
			error_log("[ldapsync] Failed to bind to $ldaphost : $ldapport with user $ldapuser");
		}

		ldap_close($ldap);
	}

	private function LDAPPasswordCheck($username, $password)
	{
		return true;

		if (!isset($username) || !isset($password) || strlen($password) == 0)
			return false;

		$ldaphost = Config::get('goonauth.ldapHost');
		$ldapport = Config::get('goonauth.ldapPort');

		// Connect to our LDAP server.
		$ldap = ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldapsync] Could not connect to $ldaphost");
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
