<?php

class GroupController extends BaseController
{
	public function showGroup($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
			return Redirect::to('/');

		$include = array('auth' => $auth, 'group' => $group);
		return View::make('group.list', $include);
	}

	public function showAuth($grid)
	{
		$auth = Session::get('auth');
		$group = Group::find($grid);
		if (empty($group))
			return Redirect::to('/');

		$include = array('auth' => $auth, 'group' => $group);
		return View::make('group.auth', $include);
	}

	public function doAuth($grid, $uid)
	{
		$action = Input::get('action');
		$group = Group::find($grid);
		$user = User::find($uid);
		$maildata = array();

		if (strcasecmp($action, "approve") == 0)
		{
			$active = UserStatus::where('USCode', 'ACTI')->first();
			$user->USID = $active->USID;
			$user->save();

			$password = sha1('dick' . intval(rand()) . 'butt');
			$password = substr($password, 0, 13);

			$this->LDAPExecute(function($ldap) {
				// Generate a salt.
				$salt = md5(uniqid(rand(), TRUE));
				$salt = substr($salt, 0, 4);

				// Generate the new password hash.
				$pass = html_entity_decode($password);
				$pass = "{SSHA}" . base64_encode(pack("H*", sha1($pass . $salt)) . $salt);

				// Record the data for our new entry.
				$info['cn'] = $user->UGoonID;
				$info['sn'] = $user->UGoonID;
				$info['uid'] = $user->UGoonID;
				$info['displayName'] = $user->UGoonID;
				$info['userPassword'] = $pass;
				$info['mail'] = $user->UEmail;

				// Add the user!
				$userdn = "cn=" . $user->UGoonID . "," . Config::get('goonauth.ldapDN');
				if (!ldap_add($ldap, $userdn, $info))
					error_log("[ldap] Failed to create user $info['cn']");

				// Add the user to the forums LDAP group.
				$forumdn = "cn=ForumMembers" . "," . Config::get('goonauth.ldapGroupDN');
				if (!ldap_mod_add($ldap, $forumdn, array('members' => $userdn)))
					error_log("[ldap] Failed to add user to forum group.");

				// Add the user to the LDAP group for their group.
				if (!is_null($group->GRLDAPGroup))
				{
					$forumdn = "cn=". $group->GRLDAPGroup . "," . Config::get('goonauth.ldapGroupDN');
					if (!ldap_mod_add($ldap, $forumdn, array('members' => $userdn)))
						error_log("[ldap] Failed to add user to forum group.");
				}
			});

			// Connect to IPB to create the forum entry.
			file_get_contents("https://forums.goonrathi.com/index.php?app=core&module=global&section=login&do=process&auth_key=880ea6a14ea49e853634fbdc5015a024&ips_username={$user->UGoonID}&ips_password={$password}");

			// Assemble the data required for the e-mail.
			$maildata = array_add($maildata, 'goonid', $user->UGoonID);
			$maildata = array_add($maildata, 'group', $user->group->GRName);
			$maildata = array_add($maildata, 'password', $password);

			// Send out the onboarding e-mail.
			Mail::send('emails.group.register.approve', $maildata, function($msg) {
				$msg->subject('Your Goonrathi / Word of Lowtax membership request was approved!');
				$msg->to($user->UEmail);
			});
		}
		else
		{
			$rejected = UserStatus::where('USCode', 'REJE')->first();
			$user->USID = $rejected->USID;
			$user->save();

			$maildata = array_add($maildata, 'reason', 'You suck');

			Mail::send('emails.group.register.deny', $maildata, function($msg) {
				$msg->subject('Your Goonrathi / Word of Lowtax membership request was DENIED!');
				$msg->to($user->UEmail);
			});
		}

		return Response::json(array(
			'success' => true
		));
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
}
