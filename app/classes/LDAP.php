<?php

class LDAP
{
	public static function Execute($func)
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

	public static function PasswordCheck($username, $password)
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
