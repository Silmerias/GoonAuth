<?php

namespace App\Extensions\LDAP;

class LDAP
{
	public static function Execute($func)
	{
		if (!config('ldap.enabled'))
			return;

		$ldaphost = config('ldap.host');
		$ldapport = config('ldap.port');

		// Connect to our LDAP server.
		$ldap = @ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldap] Could not connect to $ldaphost");
			return;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = config('ldap.admin.user');
		$ldappass = config('ldap.admin.password');

		// If nothing was entered, use NULL so we do an anonymous bind.
		if (strlen($ldapuser) === 0)
			$ldapuser = NULL;
		if (strlen($ldappass) === 0)
			$ldappass = NULL;

		// Attempt to bind now.
		if (@ldap_bind($ldap, $ldapuser, $ldappass))
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

	public static function PasswordCheck($username, $password)
	{
		if (!config('ldap.enabled'))
			return true;

		if (!isset($username) || !isset($password) || strlen($password) == 0)
			return false;

		$ldaphost = config('ldap.host');
		$ldapport = config('ldap.port');

		// Connect to our LDAP server.
		$ldap = @ldap_connect($ldaphost, $ldapport);
		if (!$ldap)
		{
			error_log("[ldap] Could not connect to $ldaphost");
			return false;
		}

		// Set options.
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

		// Grab some settings.
		$ldapuser = "cn=" . $username . "," . config('ldap.dn.users');
		$ldappass = $password;

		// Attempt to bind now.
		$ret = false;
		try
		{
			if (@ldap_bind($ldap, $ldapuser, $ldappass))
				$ret = true;
		}
		catch (Exception $e) {}

		ldap_close($ldap);
		return $ret;
	}
}
